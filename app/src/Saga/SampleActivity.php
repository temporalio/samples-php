<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Saga;

use Psr\Log\LoggerInterface;
use Temporal\SampleUtils\Logger;

class SampleActivity implements SampleActivityInterface
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function execute(int $amount)
    {
        $this->log('execute amount=%s', $amount);
    }

    public function compensate(int $amount)
    {
        $this->log('compensate amount=%s', $amount);
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