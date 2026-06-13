<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Temporal\DataConverter\Type;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\Workflow\ReturnType;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface TestHelloWithTokenCallerWorkflow
{
    #[WorkflowMethod]
    #[ReturnType(Type::TYPE_ARRAY)]
    public function hello(string $endpoint, string $name, Language $language);
}
