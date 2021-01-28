<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\UpdatableTimer;

use Temporal\Workflow\QueryMethod;
use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface DynamicSleepWorkflowInterface
{
    public const WORKFLOW_ID = 'updatable-timer';

    #[WorkflowMethod]
    public function execute(
        int $wakeUpTime
    );

    #[SignalMethod]
    public function updateWakeUpTime(
        int $wakeUpTime
    ): void;

    #[QueryMethod]
    public function getWakeUpTime(): int;
}