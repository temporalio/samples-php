<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\Nexus\Workflow\TestMultiArgsHelloCallerWorkflow;
use Temporal\Samples\NexusMultipleArguments\Service\Language;

/**
 * End-to-end run of the NexusMultipleArguments sample: a single-DTO Nexus
 * contract backed by a workflow that takes two positional arguments.
 */
final class MultipleArgumentsTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-multiargs';

    public function testHelloUnpacksDtoIntoWorkflowArguments(): void
    {
        $workflow = $this->newCaller(TestMultiArgsHelloCallerWorkflow::class);

        $result = $workflow->hello($this->endpoint['name'], 'World', Language::ES);

        self::assertSame('¡Hola! World 👋', $result);
    }
}
