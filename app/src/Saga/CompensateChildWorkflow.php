<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Saga;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

class CompensateChildWorkflow implements CompensateChildWorkflowOperation
{
    private $activity;

    public function __construct()
    {
        $this->activity = Workflow::newActivityStub(
            SampleActivityInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(2))
        );
    }

    public function compensate(int $amount)
    {
        return yield $this->activity->compensate($amount);
    }
}