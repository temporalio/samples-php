<?php

declare(strict_types=1);

namespace Temporal\Samples\InfrequentPolling;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface GreetingWorkflowInterface
{
    #[WorkflowMethod(name: 'InfrequentPolling.greet')]
    public function greet(
        string $name
    ): \Generator;
}
