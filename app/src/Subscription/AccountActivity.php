<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Subscription;

use Psr\Log\LoggerInterface;
use Temporal\SampleUtils\Logger;

class AccountActivity implements AccountActivityInterface
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function sendWelcomeEmail(string $userID): void
    {
        $this->log('Send welcome email to %s', $userID);
    }

    public function chargeMonthlyFee(string $userID): void
    {
        $this->log('Charge %s of monthly fee', $userID);
    }

    public function sendEndOfTrialEmail(string $userID): void
    {
        $this->log('Send %s end of trial email', $userID);
    }

    public function sendMonthlyChargeEmail(string $userID): void
    {
        $this->log('Send %s monthly charge email', $userID);
    }

    public function sendSorryToSeeYouGoEmail(string $userID): void
    {
        $this->log('Send %s sorry to see you go email', $userID);
    }

    public function processSubscriptionCancellation(string $userID): void
    {
        $this->log('Cancel subscription for %s', $userID);
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