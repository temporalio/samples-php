<?php

declare(strict_types=1);

namespace Temporal\Samples\Nexus\Caller;

use Temporal\Samples\Nexus\Service\Language;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface HelloWithTokenCallerWorkflow
{
    #[WorkflowMethod]
    public function hello(string $message, Language $language);
}
