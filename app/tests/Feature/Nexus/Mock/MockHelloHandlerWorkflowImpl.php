<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Mock;

use Temporal\Samples\Nexus\Handler\HelloHandlerWorkflow;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;

/**
 * Replaces {@see \Temporal\Samples\Nexus\Handler\HelloHandlerWorkflowImpl}
 * for tests. PHP doesn't have JUnit's `registerWorkflowImplementationFactory`
 * mock-per-test pattern — to swap behaviour, register this class on a
 * dedicated task queue instead of the production one.
 */
class MockHelloHandlerWorkflowImpl implements HelloHandlerWorkflow
{
    public const CANNED = 'Hello Mock World 👋';

    public function hello(HelloInput $input): HelloOutput
    {
        return new HelloOutput(self::CANNED);
    }
}
