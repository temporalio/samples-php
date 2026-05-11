<?php

declare(strict_types=1);

namespace Temporal\Samples\InfrequentPolling;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Workflow;

class GreetingWorkflow implements GreetingWorkflowInterface
{
    private ActivityProxy|ComposeGreetingActivityInterface $activity;

    public function __construct()
    {
        $this->activity = Workflow::newActivityStub(
            ComposeGreetingActivityInterface::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::seconds(2))
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withInitialInterval(CarbonInterval::seconds(60))
                        ->withBackoffCoefficient(1.0)
                )
        );
    }

    public function greet(
        string $name
    ): \Generator {
        return yield $this->activity->composeGreeting('Hello', $name);
    }
}
