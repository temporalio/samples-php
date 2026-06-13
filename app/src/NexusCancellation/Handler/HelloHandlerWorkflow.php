<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusCancellation\Handler;

use Temporal\Samples\NexusCancellation\Service\HelloInput;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface HelloHandlerWorkflow
{
    #[WorkflowMethod]
    public function hello(HelloInput $input);
}
