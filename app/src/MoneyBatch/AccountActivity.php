<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\MoneyBatch;

class AccountActivity implements AccountInterface
{
    public function deposit(string $accountId, string $referenceId, int $amountCents): void
    {
        $this->log(
            "Withdraw to %s of %d cents requested. ReferenceId=%s\n",
            $accountId,
            $amountCents,
            $referenceId
        );
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
        // by default all error logs are forwarded to the application server log and docker log
        error_log(sprintf($message, ...$arg));
    }
}