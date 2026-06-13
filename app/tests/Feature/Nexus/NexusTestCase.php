<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\TestCase;
use Carbon\CarbonInterval;
use Temporal\Client\WorkflowOptions;

/**
 * Base for Nexus feature tests: creates a dedicated Nexus endpoint targeting
 * `static::TASK_QUEUE` before each test and drops it afterwards.
 */
abstract class NexusTestCase extends TestCase
{
    public const TASK_QUEUE = '';

    protected NexusEndpointHelper $nexusHelper;

    /** @var array{id: string, name: string} */
    protected array $endpoint;

    protected function setUp(): void
    {
        parent::setUp();

        if (static::TASK_QUEUE === '') {
            self::fail(\sprintf('%s must override the TASK_QUEUE constant.', static::class));
        }

        $this->nexusHelper = new NexusEndpointHelper(\getenv('TEMPORAL_ADDRESS') ?: 'localhost:7236');
        $this->endpoint = $this->nexusHelper->setupEndpoint(
            namespace: 'default',
            taskQueue: static::TASK_QUEUE,
        );
    }

    protected function tearDown(): void
    {
        $this->nexusHelper->deleteEndpoint($this->endpoint['id']);
        parent::tearDown();
    }

    /**
     * @template T of object
     * @param class-string<T> $workflowClass
     * @return T
     */
    protected function newCaller(string $workflowClass, ?WorkflowOptions $options = null): object
    {
        return $this->workflowClient->newWorkflowStub(
            $workflowClass,
            ($options ?? WorkflowOptions::new())
                ->withTaskQueue(static::TASK_QUEUE)
                ->withWorkflowExecutionTimeout(CarbonInterval::seconds(60)),
        );
    }
}
