<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Caller;

use Temporal\Samples\NexusContextPropagation\Service\Language;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface HelloCallerWorkflow
{
    #[WorkflowMethod]
    public function hello(string $message, Language $language);
}
