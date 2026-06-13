<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\Nexus\Mock\MockHelloHandlerWorkflowImpl;
use App\Tests\Feature\Nexus\Mock\MockSampleNexusServiceImpl;
use App\Tests\Feature\Nexus\Workflow\TestEchoCallerWorkflow;
use App\Tests\Feature\Nexus\Workflow\TestHelloCallerWorkflow;
use Temporal\Samples\Nexus\Service\Language;

/**
 * The entire Nexus service implementation is replaced by a test double.
 * Mirrors the Java `NexusServiceMockTest` style.
 */
final class NexusServiceMockTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-mock-service';

    public function testEchoFromMockedService(): void
    {
        $workflow = $this->newCaller(TestEchoCallerWorkflow::class);

        $result = $workflow->echo($this->endpoint['name'], 'ignored');

        self::assertSame(MockSampleNexusServiceImpl::ECHO_CANNED, $result);
    }

    public function testHelloFromMockedService(): void
    {
        $workflow = $this->newCaller(TestHelloCallerWorkflow::class);

        $result = $workflow->hello($this->endpoint['name'], 'World', Language::DE);

        self::assertSame(MockHelloHandlerWorkflowImpl::CANNED, $result);
    }
}
