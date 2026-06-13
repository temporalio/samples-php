<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface TestManualJobCallerWorkflow
{
    #[WorkflowMethod]
    public function run(string $endpoint, string $jobName);
}
