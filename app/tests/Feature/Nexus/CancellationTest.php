<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\Nexus\Workflow\TestCancellationCallerWorkflow;

/**
 * End-to-end run of the NexusCancellation sample flow: fan out the same
 * operation in five languages, keep the first reply, cancel the rest.
 */
final class CancellationTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-cancellation';

    public function testFirstReplyWinsAndOthersAreCancelled(): void
    {
        $workflow = $this->newCaller(TestCancellationCallerWorkflow::class);

        $result = $workflow->hello($this->endpoint['name'], 'Nexus');

        self::assertStringEndsWith(' [cancelled=4]', $result);
        self::assertContains(\substr($result, 0, -\strlen(' [cancelled=4]')), [
            'Hello Nexus 👋',
            'Bonjour Nexus 👋',
            'Hallo Nexus 👋',
            '¡Hola! Nexus 👋',
            'Merhaba Nexus 👋',
        ]);
    }
}
