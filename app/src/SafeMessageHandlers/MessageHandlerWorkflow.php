<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers;

use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Samples\SafeMessageHandlers\DTO\AssignNodesToJobInput;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerAssignNodesToJobInput;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerAssignNodesToJobResult;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerDeleteJobInput;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerInput;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerResult;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerState;
use Temporal\Samples\SafeMessageHandlers\DTO\FindBadNodesInput;
use Temporal\Samples\SafeMessageHandlers\DTO\UnassignNodesForJobInput;
use Temporal\Workflow;
use Temporal\Workflow\Mutex;

class MessageHandlerWorkflow implements MessageHandlerWorkflowInterface
{
    private ClusterManagerState $state;
    private Mutex $nodesLock;
    private ?int $maxHistoryLength;
    private int $sleepIntervalSeconds;

    #[Workflow\WorkflowInit]
    public function __construct(ClusterManagerInput $input) {
        $this->state = $input->state ?? new ClusterManagerState();
        $this->maxHistoryLength = $input->testContinueAsNew ? 120 : null;
        $this->sleepIntervalSeconds = $input->testContinueAsNew ? 1 : 600;

        // Protects workflow state from interleaved access
        $this->nodesLock = new Mutex();
    }

    public function run(ClusterManagerInput $input)
    {
        yield Workflow::await(fn() => $this->state->clusterStarted);
        // Perform health checks at intervals.
        while (true) {
            yield $this->performHealthChecks();
            try {
                yield Workflow::awaitWithTimeout(
                    $this->sleepIntervalSeconds,
                    fn() => $this->state->clusterShutdown || $this->shouldContinueAsNew(),
                );
            } catch (\Throwable) {
                // do nothing
            }

            if ($this->state->clusterShutdown) {
                break;
            }
            if ($this->shouldContinueAsNew()) {
                // We don't want to leave any job assignment or deletion handlers half-finished when we continue as new.
                yield Workflow::await(fn() => Workflow::allHandlersFinished());
                trap("Continuing as new");
                Workflow::continueAsNew(
                    Workflow::getInfo()->type->name,
                    [new ClusterManagerInput($this->state, $input->testContinueAsNew)],
                );
            }
        }

        // Make sure we finish off handlers such as deleting jobs before we complete the workflow.
        yield Workflow::await(fn() => Workflow::allHandlersFinished());
        return new ClusterManagerResult(
            \count($this->getAssignedNodes()),
            \count($this->getBadNodes()),
        );
    }

    public function startCluster(): void
    {
        $this->state->clusterStarted = true;
        $this->state->nodes = \array_fill_keys(\range(0, 24), null);
        trap("Cluster started");
    }

    public function shutdownCluster()
    {
        yield Workflow::await(fn() => $this->state->clusterStarted);
        $this->state->clusterShutdown = true;
        trap("Cluster shut down");
    }

    public function assignNodesToJob(ClusterManagerAssignNodesToJobInput $input)
    {
        yield Workflow::await(fn() => $this->state->clusterStarted);
        // If you want the client to receive a failure, either add an update validator
        // and throw the exception from there.
        // Other exceptions in the main handler will cause the workflow to keep retrying and get it stuck.
        $this->state->clusterShutdown and throw new ApplicationFailure(
            'Cannot assign nodes to a job: Cluster is already shut down', 'CannotAssignNodesToJob', true,
        );

        return yield Workflow::runLocked($this->nodesLock, function () use ($input) {
            // Idempotency guard.
            if (\in_array($input->jobName, $this->state->jobsAssigned, true)) {
                return new ClusterManagerAssignNodesToJobResult(
                    $this->getAssignedNodes($input->jobName),
                );
            }

            $unassignedNodes = $this->getUnassignedNodes();
            \count($unassignedNodes) < $input->totalNumNodes and throw new ApplicationFailure(
                \sprintf(
                    'Cannot assign %d nodes; have only %d available',
                    $input->totalNumNodes,
                    \count($unassignedNodes),
                ),
                'CannotAssignNodesToJob',
                true,
            );

            $nodesToAssign = \array_slice($unassignedNodes, 0, $input->totalNumNodes);
            /**
             * This await would be dangerous without {@see self::$nodesLock} because it yields control
             * and allows interleaving with {@see self::deleteJob()} and {@see self::performHealthChecks()},
             * which both touch {@see ClusterManagerState::$nodes}.
             */
            yield $this->_assignNodesToJob($nodesToAssign, $input->jobName);
            return new ClusterManagerAssignNodesToJobResult($this->getAssignedNodes($input->jobName));
        });
    }

    public function deleteJob(ClusterManagerDeleteJobInput $input): \Generator
    {
        yield Workflow::await(fn() => $this->state->clusterStarted);
        // If you want the client to receive a failure, either add an update validator
        // and throw the exception from there
        $this->state->clusterShutdown and throw new ApplicationFailure(
            'Cannot delete a job: Cluster is already shut down', 'CannotDeleteJob', true,
        );

        return yield Workflow::runLocked($this->nodesLock, function () use ($input) {
            $nodesToUnassign = \array_keys(\array_filter($this->state->nodes, fn($v) => $v === $input->jobName));
            /**
             * This await would be dangerous without {@see self::$nodesLock} because it yields control
             * and allows interleaving with {@see self::assignNodesToJob()} and {@see self::performHealthChecks()},
             * which all touch {@see ClusterManagerState::$nodes}.
             */
            yield $this->_unassignNodesForJob($nodesToUnassign, $input->jobName);
        });
    }

    public function getState(): ClusterManagerState
    {
        return $this->state;
    }

    private function getUnassignedNodes(): array
    {
        return \array_keys(\array_filter($this->state->nodes, fn($v) => $v === null));
    }

    private function getBadNodes(): array
    {
        return \array_keys(\array_filter($this->state->nodes, fn($v) => $v === 'BAD!'));
    }

    private function getAssignedNodes(?string $jobName = null): array
    {
        return $jobName === null
            ? \array_keys(\array_filter($this->state->nodes, fn($v) => $v !== null && $v !== 'BAD!'))
            : \array_keys(\array_filter($this->state->nodes, fn($v) => $v === $jobName));
    }

    private function performHealthChecks(): \Generator
    {
        return yield Workflow::runLocked($this->nodesLock, function () {
            $assignedNodes = $this->getAssignedNodes();
            try {
                /**
                 * This await would be dangerous without {@see self::$nodesLock} because it yields control
                 * and allows interleaving with {@see self::assignNodesToJob()} and {@see self::deleteJob()},
                 * which both touch {@see ClusterManagerState::$nodes}.
                 */
                $badNodes = yield Workflow::executeActivity(
                    'find_bad_nodes',
                    [new FindBadNodesInput($assignedNodes)],
                    ActivityOptions::new()->withStartToCloseTimeout('10 seconds')
                        // This health check is optional, and our lock would block the whole workflow
                        ->withRetryOptions(RetryOptions::new()->withMaximumAttempts(1)),
                );

                foreach ($badNodes as $node) {
                    $this->state->nodes[$node] = 'BAD!';
                }
            } catch (\Throwable $e) {
                trap(\sprintf('Health check failed with error %s: %s', $e::class, $e->getMessage()));
            }
        });
    }

    private function shouldContinueAsNew(): bool
    {
        if (Workflow::getInfo()->shouldContinueAsNew) {
            return true;
        }

        // This is just for ease-of-testing.  In production, we trust temporal to tell us when to continue as new.
        if ($this->maxHistoryLength !== null && Workflow::getInfo()->historyLength > $this->maxHistoryLength) {
            return true;
        }

        return false;
    }

    private function _unassignNodesForJob(array $nodesToUnassign, string $jobName): \Generator
    {
        yield Workflow::executeActivity(
            'unassign_nodes_for_job',
            [new UnassignNodesForJobInput($nodesToUnassign, $jobName)],
            ActivityOptions::new()->withStartToCloseTimeout('10 seconds'),
        );

        foreach ($nodesToUnassign as $node) {
            $this->state->nodes[$node] = null;
        }
    }

    private function _assignNodesToJob(array $assignedNodes, string $jobName): \Generator
    {
        yield Workflow::executeActivity(
            'assign_nodes_to_job',
            [new AssignNodesToJobInput($assignedNodes, $jobName)],
            ActivityOptions::new()->withStartToCloseTimeout('10 seconds'),
        );
        foreach ($assignedNodes as $node) {
            $this->state->nodes[$node] = $jobName;
        }

        $this->state->jobsAssigned[] = $jobName;
    }
}
