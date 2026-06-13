<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\Nexus\Workflow\TestManualJobCallerWorkflow;

/**
 * End-to-end run of the NexusManualOperation sample flow: a manual
 * OperationHandlerInterface-backed operation answers synchronously on the
 * fast-path, then asynchronously with its own token, and honours cancel.
 */
final class ManualOperationTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-manual';

    public function testSyncFastPathTokenAndCancel(): void
    {
        $workflow = $this->newCaller(TestManualJobCallerWorkflow::class);

        $result = $workflow->run($this->endpoint['name'], 'Demo');

        self::assertStringContainsString('done instantly: Demo', $result);
        self::assertStringContainsString('[token=job-', $result);
        self::assertStringEndsWith('cancelled', $result);
    }
}
