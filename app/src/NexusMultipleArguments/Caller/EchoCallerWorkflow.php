<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Caller;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface EchoCallerWorkflow
{
    #[WorkflowMethod]
    public function echo(string $message);
}
