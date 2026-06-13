<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

/**
 * Test-only echo caller. Production {@see \Temporal\Samples\Nexus\Caller\EchoCallerWorkflow}
 * hardcodes `CallerWorker::ENDPOINT_NAME`; for tests we want the endpoint to
 * be supplied at runtime so each test can route to its own task queue.
 */
#[WorkflowInterface]
interface TestEchoCallerWorkflow
{
    #[WorkflowMethod]
    public function echo(string $endpoint, string $message);
}
