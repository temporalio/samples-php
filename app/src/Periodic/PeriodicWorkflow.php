<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Periodic;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

class PeriodicWorkflow implements PeriodicWorkflowInterface
{
    /**
     * This value is so low just to make the example interesting to watch. In real life you would
     * use something like 100 or a value that matches a business cycle. For example if it runs once
     * an hour 24 would make sense.
     */
    private const CONTINUE_AS_NEW_FREQUENCY = 10;

    private $greetingActivity;

    public function __construct()
    {
        $this->greetingActivity = Workflow::newActivityStub(
            GreetingActivityInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(10))
        );
    }

    public function greetPeriodically(string $name, int $count = 0)
    {
        // Loop the predefined number of times then continue this workflow as new.
        // This is needed to periodically truncate the history size.
        for ($i = 0; $i < self::CONTINUE_AS_NEW_FREQUENCY; $i++) {
            // counter passed between workflow runs
            $count++;

            $delayMillis = yield Workflow::sideEffect(fn() => random_int(10, 10000));
            yield $this->greetingActivity->greet(
                sprintf('Hello %s! Sleeping for %s milliseconds.', $name, $delayMillis)
            );

            if (!Workflow::isReplaying()) {
                file_put_contents('php://stderr', sprintf('Count so far %s', $count));
            }

            yield Workflow::timer(CarbonInterval::milliseconds($delayMillis));
        }

        // Current workflow run stops executing after this call.
        return Workflow::newContinueAsNewStub(self::class)->greetPeriodically($name, $count);
    }
}