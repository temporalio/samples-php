<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface TestContextEchoCallerWorkflow
{
    #[WorkflowMethod]
    public function echo(string $endpoint, string $message);
}
