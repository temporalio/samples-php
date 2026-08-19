<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use Temporal\Samples\Nexus\Caller\HelloCallerWorkflow;
use Temporal\Samples\Nexus\Caller\EchoCallerWorkflow;
use Temporal\Samples\Nexus\Caller\CallerWorker;
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
    public const ENDPOINT_NAME = CallerWorker::ENDPOINT_NAME;

    public function testEchoRoundTrip(): void
    {
        $workflow = $this->newCaller(EchoCallerWorkflow::class);

        $result = $workflow->echo('Hello');

        self::assertSame('Hello', $result);
    }

    public function testHelloRoundTrip(): void
    {
        $workflow = $this->newCaller(HelloCallerWorkflow::class);

        $result = $workflow->hello('World', Language::EN);

        self::assertSame('Hello World 👋', $result);
    }

    public function testHelloRespectsLanguage(): void
    {
        $workflow = $this->newCaller(HelloCallerWorkflow::class);

        $result = $workflow->hello('Nexus', Language::ES);

        self::assertSame('¡Hola! Nexus 👋', $result);
    }
}
