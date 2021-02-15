<?php

/**
 * This file is part of the Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\AsyncActivityCompletion;

use Psr\Log\LoggerInterface;
use Temporal\Activity;
use Temporal\SampleUtils\Logger;


// @@@SNIPSTART samples-php-async-activity-completion-activity-class
class GreetingActivity implements GreetingActivityInterface
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }
    /**
     * Demonstrates how to implement an Activity asynchronously.
     * When {@link Activity::doNotCompleteOnReturn()} is called,
     * the Activity implementation function that returns doesn't complete the Activity.
     */
    public function composeGreeting(string $greeting, string $name): string
    {
        // In real life this request can be executed anywhere. By a separate service for example.
        $this->logger->info(sprintf('GreetingActivity token: %s', base64_encode(Activity::getInfo()->taskToken)));
        // Send the taskToken to the external service that will complete the Activity.
        // Return from the Activity a function indicating that Temporal should wait
        // for an async completion message.
        Activity::doNotCompleteOnReturn();
        // When doNotCompleteOnReturn() is invoked the return value is ignored.
        return 'ignored';
    }
}
// @@@SNIPEND
