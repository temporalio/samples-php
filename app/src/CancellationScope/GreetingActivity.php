<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\CancellationScope;

use Psr\Log\LoggerInterface;
use Temporal\Activity;
use Temporal\Exception\Client\ActivityCompletionException;
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
        $random = random_int(2, 30);
        $this->log('Activity for %s going to take %s seconds', $greeting, $random);

        for ($i = 0; $i < $random; $i++) {
            sleep(1);
            try {
                Activity::heartbeat($i);
            } catch (ActivityCompletionException $e) {
                // There are multiple reasons for heartbeat throwing an exception.
                // All of them are modeled as subclasses of the ActivityCompletionException.
                // The main three reasons are:
                // * activity cancellation,
                // * activity not existing (due to timeout for example) from the service point of view
                // * worker shutdown requested

                // Simulate cleanup
                $random = random_int(2, 5);

                $this->log(
                    "Activity for %s was cancelled. Cleanup is expected to take %s seconds.",
                    $greeting,
                    $random
                );
                sleep($random);

                $this->log("Activity for %s finished with cancellation", $greeting);

                throw $e;
            }
        }

        $this->log('Activity for %s completed', $greeting);

        return $greeting . " " . $name . "!";
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