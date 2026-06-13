<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface TestCancellationCallerWorkflow
{
    #[WorkflowMethod]
    public function hello(string $endpoint, string $message);
}
