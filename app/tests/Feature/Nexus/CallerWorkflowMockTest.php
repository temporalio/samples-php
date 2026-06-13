<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\Nexus\Mock\MockEchoClient;
use App\Tests\Feature\Nexus\Mock\MockHelloHandlerWorkflowImpl;
use App\Tests\Feature\Nexus\Workflow\TestEchoCallerWorkflow;
use App\Tests\Feature\Nexus\Workflow\TestHelloCallerWorkflow;
use Temporal\Samples\Nexus\Service\Language;

/**
 * Production `SampleNexusServiceImpl` with its `EchoClient` dependency mocked
 * and the handler workflow replaced by a canned one. Mirrors the Java
 * `CallerWorkflowMockTest` style.
 */
final class CallerWorkflowMockTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-mock-handler';

    public function testEchoUsesMockedClient(): void
    {
        $workflow = $this->newCaller(TestEchoCallerWorkflow::class);

        $result = $workflow->echo($this->endpoint['name'], 'ignored');

        self::assertSame(MockEchoClient::CANNED, $result);
    }

    public function testHelloUsesMockHandlerWorkflow(): void
    {
        $workflow = $this->newCaller(TestHelloCallerWorkflow::class);

        $result = $workflow->hello($this->endpoint['name'], 'World', Language::EN);

        self::assertSame(MockHelloHandlerWorkflowImpl::CANNED, $result);
    }
}
