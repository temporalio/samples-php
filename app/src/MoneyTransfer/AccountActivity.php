<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\MoneyTransfer;

use Psr\Log\LoggerInterface;
use Temporal\SampleUtils\Logger;

class AccountActivity implements AccountInterface
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function deposit(string $accountId, string $referenceId, int $amountCents): void
    {
        $this->log(
            "Withdraw to %s of %d cents requested. ReferenceId=%s\n",
            $accountId,
            $amountCents,
            $referenceId
        );

        // throw new \RuntimeException("simulated"); // Uncomment to simulate failure
    }

    public function withdraw(string $accountId, string $referenceId, int $amountCents): void
    {
        $this->log(
            "Deposit to %s of %d cents requested. ReferenceId=%s\n",
            $accountId,
            $amountCents,
            $referenceId
        );
    }

    /**
     * @param string $message
     * @param mixed ...$arg
     */
    private function log(string $message, ...$arg)
    {
        $this->logger->debug(sprintf($message, ...$arg));
    }
}