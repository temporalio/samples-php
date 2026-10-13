<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use Temporal\Samples\NexusManualOperation\Caller\JobCallerWorkflow;
use Temporal\Samples\NexusManualOperation\Caller\CallerWorker;


/**
 * End-to-end run of the NexusManualOperation sample flow: a manual
 * OperationHandlerInterface-backed operation answers synchronously on the
 * fast-path, then asynchronously with its own token, and honours cancel.
 */
final class ManualOperationTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-manual';
    public const ENDPOINT_NAME = CallerWorker::ENDPOINT_NAME;

    public function testSyncFastPathTokenAndCancel(): void
    {
        $workflow = $this->newCaller(JobCallerWorkflow::class);

        $result = $workflow->run('Demo');

        self::assertStringContainsString('done instantly: Demo', $result);
        self::assertStringContainsString('[token=job-', $result);
        self::assertStringEndsWith('cancelled', $result);
    }
}
