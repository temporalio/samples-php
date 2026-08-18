<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Carbon\CarbonInterval;
use Temporal\Exception\Failure\CanceledFailure;
use Temporal\Exception\Failure\NexusOperationFailure;
use Temporal\Samples\NexusManualOperation\Service\JobInput;
use Temporal\Samples\NexusManualOperation\Service\JobResult;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationCancellationType;
use Temporal\Workflow\NexusOperationHandle;
use Temporal\Workflow\NexusOperationOptions;

/**
 * Same flow as the NexusManualOperation sample caller — sync fast-path, then
 * async with handler-issued token + cancel — with the endpoint passed in so
 * each test run can use its own endpoint.
 */
class TestManualJobCallerWorkflowImpl implements TestManualJobCallerWorkflow
{
    public function run(string $endpoint, string $jobName)
    {
        $stub = Workflow::newUntypedNexusOperationStub(
            NexusOperationOptions::new()
                ->withEndpoint($endpoint)
                ->withService('SampleNexusService')
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(20))
                ->withCancellationType(NexusOperationCancellationType::TryCancel),
        );

        /** @var NexusOperationHandle<JobResult> $handle */
        $handle = yield $stub->start(
            'startJob',
            [new JobInput($jobName, instant: true)],
            JobResult::class,
        );

        if ($handle->getOperationToken() !== null) {
            throw new \LogicException('Sync result must not carry an operation token.');
        }

        /** @var JobResult $instantResult */
        $instantResult = yield $handle->getResult();

        $handle2 = null;
        $failure = null;
        $scope = Workflow::async(static function () use ($stub, $jobName, &$handle2, &$failure): \Generator {
            try {
                $handle2 = yield $stub->start(
                    'startJob',
                    [new JobInput($jobName)],
                    JobResult::class,
                );
            } catch (\Throwable $e) {
                $failure = $e;
                throw $e;
            }

            yield $handle2->getResult();
        });

        yield Workflow::await(function () use (&$handle2, &$failure): bool {
            return $handle2 !== null || $failure !== null;
        });

        if ($failure !== null) {
            throw $failure;
        }

        $token2 = $handle2->getOperationToken();

        yield Workflow::timer(1);
        $scope->cancel();

        $cancelled = false;

        try {
            yield $scope;
        } catch (CanceledFailure) {
            $cancelled = true;
        } catch (NexusOperationFailure $e) {
            if (!$e->getPrevious() instanceof CanceledFailure) {
                throw $e;
            }

            $cancelled = true;
        }

        if (!$cancelled) {
            throw new \LogicException('Async operation did not end cancelled.');
        }

        return "[instant={$instantResult->message}] [token={$token2}] cancelled";
    }
}
