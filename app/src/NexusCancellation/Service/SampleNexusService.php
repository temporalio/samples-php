<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusCancellation\Service;

use Temporal\Nexus\Attribute\AsyncOperation;
use Temporal\Nexus\Attribute\Service;
use Temporal\Nexus\WorkflowHandle;

#[Service]
interface SampleNexusService
{
    #[AsyncOperation(output: HelloOutput::class)]
    public function hello(HelloInput $input): WorkflowHandle;
}
