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

    public function composeGreeting(string $greeting, string $name): string
    {
        $this->logger->info(sprintf('GreetingActivity token: %s', base64_encode(Activity::getInfo()->taskToken)));
        Activity::doNotCompleteOnReturn();

        return 'ignored';
    }
}