<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use Temporal\Samples\NexusContextPropagation\Caller\CallerWorker;
use Temporal\Samples\Nexus\Caller\EchoCallerWorkflow;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\Samples\Nexus\Caller\HelloCallerWorkflow;
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
    public const ENDPOINT_NAME = CallerWorker::ENDPOINT_NAME;

    public function testCallerWorkflowIdPropagatesViaHeaders(): void
    {
        $workflowId = 'ctx-prop-' . \bin2hex(\random_bytes(4));
        $workflow = $this->newCaller(
            EchoCallerWorkflow::class,
            WorkflowOptions::new()->withWorkflowId($workflowId),
        );

        $result = $workflow->echo('ignored');

        self::assertSame($workflowId, $result);
    }

    public function testCallerWorkflowIdReachesTheBackingWorkflow(): void
    {
        $workflowId = 'ctx-prop-async-' . \bin2hex(\random_bytes(4));
        $workflow = $this->newCaller(
            HelloCallerWorkflow::class,
            WorkflowOptions::new()->withWorkflowId($workflowId),
        );

        $result = $workflow->hello('Nexus', Language::EN);

        self::assertStringContainsString($workflowId, $result);
    }
}
