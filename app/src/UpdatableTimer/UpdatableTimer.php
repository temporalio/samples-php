<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\UpdatableTimer;

use Carbon\Carbon;
use Temporal\Workflow;

class UpdatableTimer
{
    private int $wakeUpTime;
    private bool $wakeUpTimeUpdated;

    public function sleepUntil(int $wakeUpTime)
    {
        // todo: make it easier for user
        return Workflow::async(
            function () use ($wakeUpTime) {
                $time = Carbon::createFromTimestamp($wakeUpTime);
                $this->log("sleepUntil: %s", (string)$time);

                $this->wakeUpTime = $wakeUpTime;

                while (true) {
                    $this->wakeUpTimeUpdated = false;
                    $sleepInterval = $this->wakeUpTime - Workflow::now()->getTimestamp();
                    $this->log('going to sleep for %s seconds', $sleepInterval);

                    if (!yield Workflow::awaitWithTimeout($sleepInterval, fn() => $this->wakeUpTimeUpdated)) {
                        break;
                    }
                }

                $this->log('sleep completed');
            }
        );
    }

    public function updateWakeUpTime(int $wakeUpTime): void
    {
        $this->wakeUpTime = $wakeUpTime;
        $this->wakeUpTimeUpdated = true;
    }

    public function getWakeUpTime(): int
    {
        return $this->wakeUpTime;
    }

    /**
     * @param string $message
     * @param mixed ...$arg
     */
    private function log(string $message, ...$arg)
    {
        // by default all error logs are forwarded to the application server log and docker log
        error_log(sprintf($message, ...$arg));
    }
}