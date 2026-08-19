<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Handler;

use Temporal\Samples\Nexus\Service\Language;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface HelloHandlerWorkflow
{
    #[WorkflowMethod]
    public function hello(string $name, Language $language);
}
