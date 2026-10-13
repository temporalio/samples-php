<?php

declare(strict_types=1);

namespace Temporal\Samples\Nexus\Handler;

use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface HelloHandlerWorkflow
{
    #[WorkflowMethod]
    public function hello(HelloInput $input): \Generator|HelloOutput;
}
