<?php

declare(strict_types=1);

namespace App\Tests\Feature\Workflow;

use App\Tests\Feature\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Temporal\Client\WorkflowOptions;
use Temporal\Exception\Client\WorkflowException;
use Temporal\Exception\Failure\ActivityFailure;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Samples\SimpleActivity\GreetingWorkflow;
use Temporal\Samples\SimpleActivity\GreetingWorkflowInterface;

#[CoversClass(GreetingWorkflow::class)]
final class SimpleActivityTest extends TestCase
{
    public function testSuccess(): void
    {
        $this->activityMocks->expectCompletion(
            'SimpleActivity.ComposeGreeting',
            'mocked response',
        );

        $workflow = $this->workflowClient
            ->newWorkflowStub(
                GreetingWorkflowInterface::class,
                WorkflowOptions::new()->withTaskQueue('tests'),
            );

        $run = $this->workflowClient->start($workflow, 'abc');

        $this->assertSame('mocked response', $run->getResult('string'));
    }

    public function testFail(): void
    {
        $this->activityMocks->expectFailure(
            'SimpleActivity.ComposeGreeting',
            new \Exception('mocked error'),
        );

        $workflow = $this->workflowClient
            ->newWorkflowStub(
                GreetingWorkflowInterface::class,
                WorkflowOptions::new()->withTaskQueue('tests'),
            );
        $run = $this->workflowClient->start($workflow, 'abc');

        try {
            $run->getResult('string');
            $this->fail('Expected exception');
        } catch (WorkflowException $e) {
            $activityFailure = $e->getPrevious();
            $this->assertInstanceOf(ActivityFailure::class, $activityFailure);

            $appFailure = $activityFailure->getPrevious();
            $this->assertInstanceOf(ApplicationFailure::class, $appFailure);
            $this->assertSame('mocked error', $appFailure->getOriginalMessage());
            $this->assertSame(\Exception::class, $appFailure->getType());
        }
    }
}
