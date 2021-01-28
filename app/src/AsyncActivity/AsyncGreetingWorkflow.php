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
        $hello = $this->greetingActivity->composeGreeting('Hello', $name);
        $bye = $this->greetingActivity->composeGreeting('Bye', $name);

        return (yield $hello) . "\n" . (yield $bye);
    }
}