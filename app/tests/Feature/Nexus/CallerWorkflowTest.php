<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\Nexus\Workflow\TestEchoCallerWorkflow;
use App\Tests\Feature\Nexus\Workflow\TestHelloCallerWorkflow;
use Temporal\Samples\Nexus\Service\Language;

/**
 * End-to-end smoke test of the basic Nexus sample, with the real production
 * handler (`SampleNexusServiceImpl` + `HelloHandlerWorkflowImpl`).
 *
 * Mirrors the Java {@link io.temporal.samples.nexus.caller.CallerWorkflowTest}.
 */
final class CallerWorkflowTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-real';

    public function testEchoRoundTrip(): void
    {
        $workflow = $this->newCaller(TestEchoCallerWorkflow::class);

        $result = $workflow->echo($this->endpoint['name'], 'Hello');

        self::assertSame('Hello', $result);
    }

    public function testHelloRoundTrip(): void
    {
        $workflow = $this->newCaller(TestHelloCallerWorkflow::class);

        $result = $workflow->hello($this->endpoint['name'], 'World', Language::EN);

        self::assertSame('Hello World 👋', $result);
    }

    public function testHelloRespectsLanguage(): void
    {
        $workflow = $this->newCaller(TestHelloCallerWorkflow::class);

        $result = $workflow->hello($this->endpoint['name'], 'Nexus', Language::ES);

        self::assertSame('¡Hola! Nexus 👋', $result);
    }
}
