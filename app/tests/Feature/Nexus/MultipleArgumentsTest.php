<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use Temporal\Samples\NexusMultipleArguments\Caller\HelloCallerWorkflow;
use Temporal\Samples\NexusMultipleArguments\Caller\CallerWorker;
use Temporal\Samples\Nexus\Service\Language;

/**
 * End-to-end run of the NexusMultipleArguments sample: a single-DTO Nexus
 * contract backed by a workflow that takes two positional arguments.
 */
final class MultipleArgumentsTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-multiargs';
    public const ENDPOINT_NAME = CallerWorker::ENDPOINT_NAME;

    public function testHelloUnpacksDtoIntoWorkflowArguments(): void
    {
        $workflow = $this->newCaller(HelloCallerWorkflow::class);

        $result = $workflow->hello('World', Language::ES);

        self::assertSame('¡Hola! World 👋', $result);
    }
}
