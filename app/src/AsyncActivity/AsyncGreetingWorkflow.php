<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\AsyncActivity;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

/**
 * Demonstrates asynchronous activity invocation. Requires a local instance of Temporal server to be
 * running.
 */
class AsyncGreetingWorkflow implements AsyncGreetingWorkflowInterface
{
    private $greetingActivity;

    public function __construct()
    {
        /**
         * Activity stub implements activity interface and proxies calls to it to Temporal activity
         * invocations. Because activities are reentrant, only a single stub can be used for multiple
         * activity invocations.
         */
        $this->greetingActivity = Workflow::newActivityStub(
            GreetingActivityInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(10))
        );
    }

    public function greet(string $name): \Generator
    {
        // Calling the activity method returns Promise which can be resolved later using yield keyword.
        $hello = $this->greetingActivity->composeGreeting('Hello', $name);
        $bye = $this->greetingActivity->composeGreeting('Bye', $name);

        return (yield $hello) . "\n" . (yield $bye);
    }
}