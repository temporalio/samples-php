<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\AsyncClosure;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

/**
 * Demonstrates async invocation of an entire sequence of activities. Requires a local instance of
 * Temporal server to be running.
 */
class AsyncGreetingWorkflow implements AsyncGreetingWorkflowInterface
{
    private $greetingActivity;

    public function __construct()
    {
        $this->greetingActivity = Workflow::newActivityStub(
            GreetingActivityInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(10))
        );
    }

    public function greet(string $name): \Generator
    {
        // Workflow::async runs it's activities and child workflows in a separate coroutine. Use keyword yield to merge
        // it back to parent process.

        $first = Workflow::async(
            function () use ($name) {
                $hello = yield $this->greetingActivity->composeGreeting('Hello', $name);
                $bye = yield $this->greetingActivity->composeGreeting('Bye', $name);

                return $hello . '; ' . $bye;
            }
        );

        $second = Workflow::async(
            function () use ($name) {
                $hello = yield $this->greetingActivity->composeGreeting('Hola', $name);
                $bye = yield $this->greetingActivity->composeGreeting('Chao', $name);

                return $hello . '; ' . $bye;
            }
        );

        return (yield $first) . "\n" . (yield $second);
    }
}