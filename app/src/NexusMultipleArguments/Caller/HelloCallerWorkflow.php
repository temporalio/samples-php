<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Caller;

use Temporal\Samples\NexusMultipleArguments\Service\Language;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface HelloCallerWorkflow
{
    #[WorkflowMethod]
    public function hello(string $message, Language $language);
}
