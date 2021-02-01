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
use Psr\Log\LoggerInterface;
use Temporal\SampleUtils\Logger;
use Temporal\Workflow;

class UpdatableTimer
{
    private int $wakeUpTime;
    private bool $wakeUpTimeUpdated;

    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function sleepUntil(int $wakeUpTime)
    {
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
        $this->logger->debug(sprintf($message, ...$arg));
    }
}