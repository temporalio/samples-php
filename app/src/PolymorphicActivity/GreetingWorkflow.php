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