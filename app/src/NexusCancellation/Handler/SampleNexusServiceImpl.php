<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusCancellation\Handler;

use Temporal\Client\WorkflowOptions;
use Temporal\Nexus\Nexus;
use Temporal\Nexus\WorkflowHandle;
use Temporal\Samples\NexusCancellation\Service\HelloInput;
use Temporal\Samples\NexusCancellation\Service\SampleNexusService;

final class SampleNexusServiceImpl implements SampleNexusService
{
    public function hello(HelloInput $input): WorkflowHandle
    {
        return WorkflowHandle::fromWorkflowMethod(
            HelloHandlerWorkflow::class,
            WorkflowOptions::new()->withWorkflowId(Nexus::getStartDetails()->requestId),
            $input,
        );
    }
}
