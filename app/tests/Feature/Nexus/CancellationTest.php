<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use Temporal\Api\History\V1\HistoryEvent;
use Temporal\Samples\NexusCancellation\Caller\CallerWorker;
use Temporal\Samples\NexusCancellation\Caller\HelloCallerWorkflow;

/**
 * End-to-end run of the NexusCancellation sample flow: fan out the same
 * operation in five languages, keep the first reply, cancel the rest.
 */
final class CancellationTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-cancellation';
    public const ENDPOINT_NAME = CallerWorker::ENDPOINT_NAME;

    public function testFirstReplyWinsAndOthersAreCancelled(): void
    {
        $workflow = $this->newCaller(HelloCallerWorkflow::class);
        $run = $this->workflowClient->start($workflow, 'Nexus');

        self::assertContains($run->getResult('string'), [
            'Hello Nexus 👋',
            'Bonjour Nexus 👋',
            'Hallo Nexus 👋',
            '¡Hola! Nexus 👋',
            'Merhaba Nexus 👋',
        ]);

        self::assertSame(4, $this->countCancelRequests($run->getExecution()));
    }

    private function countCancelRequests(\Temporal\Workflow\WorkflowExecution $execution): int
    {
        $count = 0;
        foreach ($this->workflowClient->getWorkflowHistory($execution) as $event) {
            \assert($event instanceof HistoryEvent);
            if ($event->hasNexusOperationCancelRequestedEventAttributes()) {
                ++$count;
            }
        }

        return $count;
    }
}
