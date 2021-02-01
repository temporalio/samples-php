<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Cron;

use Psr\Log\LoggerInterface;
use Temporal\Activity;
use Temporal\SampleUtils\Logger;

class GreetingActivity implements GreetingActivityInterface
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function composeGreeting(string $greeting, string $name): string
    {
        $this->log("compose greeting for %s", Activity::getInfo()->workflowExecution->getID());

        return $greeting . ' ' . $name;
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