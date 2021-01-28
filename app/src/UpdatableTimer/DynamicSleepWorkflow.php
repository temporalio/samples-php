<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\UpdatableTimer;

class DynamicSleepWorkflow implements DynamicSleepWorkflowInterface
{
    private UpdatableTimer $timer;

    public function __construct()
    {
        $this->timer = new UpdatableTimer();
    }

    public function execute(int $wakeUpTime)
    {
        yield $this->timer->sleepUntil($wakeUpTime);
    }

    public function updateWakeUpTime(int $wakeUpTime): void
    {
        $this->timer->updateWakeUpTime($wakeUpTime);
    }

    public function getWakeUpTime(): int
    {
        return $this->timer->getWakeUpTime();
    }
}