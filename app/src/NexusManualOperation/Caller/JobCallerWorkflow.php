<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusManualOperation\Caller;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface JobCallerWorkflow
{
    #[WorkflowMethod]
    public function run(string $jobName);
}
