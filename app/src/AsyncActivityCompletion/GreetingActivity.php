<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\AsyncActivityCompletion;

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

    /**
     * Demonstrates how to implement an activity asynchronously. When {@link Activity::doNotCompleteOnReturn()}
     * is called the activity implementation function returning doesn't complete the activity.
     */
    public function composeGreeting(string $greeting, string $name): string
    {
        // In real life this request can be executed anywhere. By a separate service for example.
        $this->logger->info(sprintf('GreetingActivity token: %s', base64_encode(Activity::getInfo()->taskToken)));

        Activity::doNotCompleteOnReturn();

        // When doNotCompleteOnReturn() is invoked the return value is ignored.

        return 'ignored';
    }
}