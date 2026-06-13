<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusManualOperation\Caller;

use Carbon\CarbonInterval;
use Temporal\Samples\NexusManualOperation\Service\JobInput;
use Temporal\Samples\NexusManualOperation\Service\JobResult;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationCancellationType;
use Temporal\Workflow\NexusOperationHandle;
use Temporal\Workflow\NexusOperationOptions;
use Temporal\Workflow\NexusOperationStubInterface;

class JobCallerWorkflowImpl implements JobCallerWorkflow
{
    private NexusOperationStubInterface $stub;

    public function __construct()
    {
        $this->stub = Workflow::newUntypedNexusOperationStub(
            NexusOperationOptions::new()
                ->withEndpoint(CallerWorker::ENDPOINT_NAME)
                ->withService('SampleNexusService')
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(20))
                ->withCancellationType(NexusOperationCancellationType::TryCancel),
        );
    }

    public function run(string $jobName)
    {
        /** @var NexusOperationHandle<JobResult> $handle */
        $handle = yield $this->stub->start(
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
        $scope = Workflow::async(function () use ($jobName, &$handle2): \Generator {
            $handle2 = yield $this->stub->start(
                'startJob',
                [new JobInput($jobName)],
                JobResult::class,
            );
            yield $handle2->getResult();
        });

        yield Workflow::await(function () use (&$handle2): bool {
            return $handle2 !== null;
        });

        $token2 = $handle2->getOperationToken();

        yield Workflow::timer(1);
        $scope->cancel();

        try {
            yield $scope;
        } catch (\Throwable) {
        }

        return "[instant={$instantResult->message}] [token={$token2}] cancelled";
    }
}
