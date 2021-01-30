<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Saga;

class SampleActivity implements SampleActivityInterface
{
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
        file_put_contents('php://stderr', sprintf($message, ...$arg));
    }
}