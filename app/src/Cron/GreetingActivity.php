<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Cron;

use Temporal\Activity;

class GreetingActivity implements GreetingActivityInterface
{
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
        file_put_contents('php://stderr', sprintf($message, ...$arg));
    }
}