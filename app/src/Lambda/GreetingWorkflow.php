<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Lambda;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

class GreetingWorkflow implements GreetingWorkflowInterface
{
    private $activity;

    public function __construct()
    {
        $this->activity = Workflow::newActivityStub(
            GreetingActivityInterface::class,
            ActivityOptions::new()->withStartToCloseTimeout(CarbonInterval::seconds(10)),
        );
    }

    public function greet(string $name)
    {
        $greeting = yield $this->activity->compose('Hello', $name);

        yield Workflow::timer(CarbonInterval::seconds(30));

        return $greeting . ' (resumed after a 30 second timer)';
    }
}
