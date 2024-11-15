<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Testing\ActivityMocker;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected WorkflowClient $workflowClient;
    protected ActivityMocker $activityMocks;

    protected function setUp(): void
    {
        $this->workflowClient = new WorkflowClient(
            ServiceClient::create(\getenv('TEMPORAL_TEST_ADDRESS')),
        );

        $this->activityMocks = new ActivityMocker();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->activityMocks->clear();
        parent::tearDown();
    }
}
