<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusCancellation\Caller;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface HelloCallerWorkflow
{
    #[WorkflowMethod]
    public function hello(string $message);
}
