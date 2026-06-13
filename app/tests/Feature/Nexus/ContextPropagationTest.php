<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\Nexus\Workflow\TestContextEchoCallerWorkflow;
use Temporal\Client\WorkflowOptions;

/**
 * Verifies the NexusContextPropagation sample plumbing end-to-end: the caller
 * stores its workflow ID in MDC, the production outbound interceptor copies it
 * into the operation headers, and the handler observes it across the Nexus
 * boundary.
 */
final class ContextPropagationTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-context';

    public function testCallerWorkflowIdPropagatesViaHeaders(): void
    {
        $workflowId = 'ctx-prop-' . \bin2hex(\random_bytes(4));
        $workflow = $this->newCaller(
            TestContextEchoCallerWorkflow::class,
            WorkflowOptions::new()->withWorkflowId($workflowId),
        );

        $result = $workflow->echo($this->endpoint['name'], 'ignored');

        self::assertSame($workflowId, $result);
    }
}
