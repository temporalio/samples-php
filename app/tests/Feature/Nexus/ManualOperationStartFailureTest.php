<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\Nexus\Mock\FailingJobClient;
use Carbon\CarbonInterval;
use Temporal\Samples\NexusManualOperation\Caller\CallerWorker;
use Temporal\Samples\NexusManualOperation\Caller\JobCallerWorkflow;
use Temporal\Client\WorkflowOptions;
use Temporal\Exception\Client\WorkflowFailedException;

/**
 * The async leg of the manual-operation caller must surface a start failure
 * instead of waiting forever for a handle that will never be assigned.
 */
final class ManualOperationStartFailureTest extends NexusTestCase
{
    public const TASK_QUEUE = 'nexus-test-manual-start-failure';
    public const ENDPOINT_NAME = CallerWorker::ENDPOINT_NAME;

    public function testAsyncStartFailureFailsTheWorkflow(): void
    {
        $workflow = $this->newCaller(
            JobCallerWorkflow::class,
            WorkflowOptions::new()->withWorkflowExecutionTimeout(CarbonInterval::seconds(15)),
        );

        try {
            $workflow->run('Demo');
            self::fail('Expected the workflow to fail with the handler error.');
        } catch (WorkflowFailedException $e) {
            self::assertStringContainsString(
                FailingJobClient::FAILURE_MESSAGE,
                (string) $e,
            );
        }
    }
}
