<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\PolymorphicActivity;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

/**
 * Demonstrates activities that extend a common interface. The core idea is that an activity
 * interface annotated with {@literal @}{@link ActivityInterface} enumerates all the methods it
 * inherited and declared and generates an activity for each of them. To avoid collisions in
 * activity names (which are by default just method names) the {@link
 * ActivityInterface#namePrefix()} or {@link ActivityMethod#name()} parameters should be used.
 */
class GreetingWorkflow implements GreetingWorkflowInterface
{
    /** @var GreetingWorkflowInterface[] */
    private $greetingActivities = [];

    public function __construct()
    {
        $this->greetingActivities = [
            Workflow::newActivityStub(
                HelloActivity::class,
                ActivityOptions::new()->withScheduleToCloseTimeout(CarbonInterval::seconds(2))
            ),
            Workflow::newActivityStub(
                ByeActivity::class,
                ActivityOptions::new()->withScheduleToCloseTimeout(CarbonInterval::seconds(2))
            ),
        ];
    }

    public function greet(string $name): \Generator
    {
        $result = [];
        foreach ($this->greetingActivities as $activity) {
            $result[] = yield $activity->composeGreeting($name);
        }

        return join("\n", $result);
    }
}