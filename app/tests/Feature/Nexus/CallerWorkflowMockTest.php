<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use Temporal\Samples\Nexus\Caller\HelloCallerWorkflow;
use Temporal\Samples\Nexus\Caller\EchoCallerWorkflow;
use Temporal\Samples\Nexus\Caller\CallerWorker;
use App\Tests\Feature\Nexus\Mock\MockEchoClient;
use App\Tests\Feature\Nexus\Mock\MockHelloHandlerWorkflowImpl;
use Temporal\Samples\Nexus\Service\Language;

/**
 * Production `SampleNexusServiceImpl` with its `EchoClient` dependency mocked
 * and the handler workflow replaced by a canned one. Mirrors the Java
 * `CallerWorkflowMockTest` style.
 */
final class CallerWorkflowMockTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-mock-handler';
    public const ENDPOINT_NAME = CallerWorker::ENDPOINT_NAME;

    public function testEchoUsesMockedClient(): void
    {
        $workflow = $this->newCaller(EchoCallerWorkflow::class);

        $result = $workflow->echo('ignored');

        self::assertSame(MockEchoClient::CANNED, $result);
    }

    public function testHelloUsesMockHandlerWorkflow(): void
    {
        $workflow = $this->newCaller(HelloCallerWorkflow::class);

        $result = $workflow->hello('World', Language::EN);

        self::assertSame(MockHelloHandlerWorkflowImpl::CANNED, $result);
    }
}
