<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusManualOperation\Handler;

use Temporal\Nexus\Handler\OperationCancelDetails;
use Temporal\Nexus\Handler\OperationContext;
use Temporal\Nexus\Handler\OperationHandlerInterface;
use Temporal\Nexus\Handler\OperationStartDetails;
use Temporal\Nexus\Handler\OperationStartResult;
use Temporal\Nexus\OperationInfo;
use Temporal\Nexus\OperationState;
use Temporal\Samples\NexusManualOperation\Service\JobInput;
use Temporal\Samples\NexusManualOperation\Service\JobResult;

/** @implements OperationHandlerInterface<JobInput, JobResult> */
final class JobOperationHandler implements OperationHandlerInterface
{
    public function __construct(
        private readonly ExternalJobClient $client,
    ) {}

    public function start(
        OperationContext $context,
        OperationStartDetails $details,
        mixed $param,
    ): OperationStartResult {
        if ($param->instant) {
            return OperationStartResult::sync(new JobResult("done instantly: {$param->jobName}"));
        }

        $jobId = $this->client->submit($param->jobName, $details->requestId);

        return OperationStartResult::async(new OperationInfo($jobId, OperationState::Running));
    }

    public function cancel(
        OperationContext $context,
        OperationCancelDetails $details,
    ): void {
        $this->client->abort($details->operationToken);
    }
}
