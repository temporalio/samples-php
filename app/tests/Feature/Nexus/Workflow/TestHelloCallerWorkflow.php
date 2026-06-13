<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Temporal\Samples\Nexus\Service\Language;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface TestHelloCallerWorkflow
{
    #[WorkflowMethod]
    public function hello(string $endpoint, string $name, Language $language);
}
