<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use App\Tests\Feature\TestCase;
use Carbon\CarbonInterval;
use Temporal\Client\WorkflowOptions;

/**
 * Base for Nexus feature tests: creates a dedicated Nexus endpoint targeting
 * `static::TASK_QUEUE` once per test class and drops it afterwards.
 */
abstract class NexusTestCase extends TestCase
{
    public const TASK_QUEUE = '';

    private static ?NexusEndpointHelper $nexusHelper = null;

    /** @var array{id: string, name: string} */
    protected array $endpoint;

    /** @var array{id: string, name: string}|null */
    private static ?array $sharedEndpoint = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (static::TASK_QUEUE === '') {
            self::fail(\sprintf('%s must override the TASK_QUEUE constant.', static::class));
        }

        self::$nexusHelper = new NexusEndpointHelper(\getenv('TEMPORAL_ADDRESS') ?: 'localhost:7236');
        self::$sharedEndpoint = self::$nexusHelper->setupEndpoint(
            namespace: 'default',
            taskQueue: static::TASK_QUEUE,
        );
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$nexusHelper !== null) {
            if (self::$sharedEndpoint !== null) {
                self::$nexusHelper->deleteEndpoint(self::$sharedEndpoint['id']);
            }

            self::$nexusHelper->close();
        }

        self::$nexusHelper = null;
        self::$sharedEndpoint = null;

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->endpoint = self::$sharedEndpoint;
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
            ($options ?? WorkflowOptions::new()->withWorkflowExecutionTimeout(CarbonInterval::seconds(60)))
                ->withTaskQueue(static::TASK_QUEUE),
        );
    }
}
