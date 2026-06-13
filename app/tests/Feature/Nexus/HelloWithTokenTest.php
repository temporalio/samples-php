<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\Nexus\Workflow\TestHelloWithTokenCallerWorkflow;
use Temporal\Samples\Nexus\Service\Language;

/**
 * Untyped Nexus stub flow: start the operation, observe the async operation
 * token before the result resolves, then await the result.
 */
final class HelloWithTokenTest extends NexusTestCase
{
    public const TASK_QUEUE = CallerWorkflowTest::TASK_QUEUE;

    public function testAsyncOperationExposesToken(): void
    {
        $workflow = $this->newCaller(TestHelloWithTokenCallerWorkflow::class);

        $result = $workflow->hello($this->endpoint['name'], 'World', Language::EN);

        self::assertNotEmpty($result['token'], 'async operation must expose an operation token');
        self::assertSame('Hello World 👋', $result['message']);
    }
}
