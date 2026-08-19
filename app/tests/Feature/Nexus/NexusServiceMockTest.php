<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use Temporal\Samples\Nexus\Caller\HelloCallerWorkflow;
use Temporal\Samples\Nexus\Caller\EchoCallerWorkflow;
use Temporal\Samples\Nexus\Caller\CallerWorker;
use App\Tests\Feature\Nexus\Mock\MockHelloHandlerWorkflowImpl;
use App\Tests\Feature\Nexus\Mock\MockSampleNexusServiceImpl;
use Temporal\Samples\Nexus\Service\Language;

/**
 * The entire Nexus service implementation is replaced by a test double.
 * Mirrors the Java `NexusServiceMockTest` style.
 */
final class NexusServiceMockTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-mock-service';
    public const ENDPOINT_NAME = CallerWorker::ENDPOINT_NAME;

    public function testEchoFromMockedService(): void
    {
        $workflow = $this->newCaller(EchoCallerWorkflow::class);

        $result = $workflow->echo('ignored');

        self::assertSame(MockSampleNexusServiceImpl::ECHO_CANNED, $result);
    }

    public function testHelloFromMockedService(): void
    {
        $workflow = $this->newCaller(HelloCallerWorkflow::class);

        $result = $workflow->hello('World', Language::DE);

        self::assertSame(MockHelloHandlerWorkflowImpl::CANNED, $result);
    }
}
