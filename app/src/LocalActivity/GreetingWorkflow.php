<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\LocalActivity;

use Carbon\CarbonInterval;
use Temporal\Activity\LocalActivityOptions;
use Temporal\Workflow;


class GreetingWorkflow implements GreetingWorkflowInterface
{
    private $greetingActivity;

    public function __construct()
    {
        $this->greetingActivity = Workflow::newActivityStub(
            GreetingActivityInterface::class,
            LocalActivityOptions::new()->withStartToCloseTimeout(CarbonInterval::seconds(2))
        );
    }

    public function greet(string $name): \Generator
    {
        // This is a blocking call that returns only after the activity has completed.
        return yield $this->greetingActivity->composeGreeting('Hello', $name);
    }
}
