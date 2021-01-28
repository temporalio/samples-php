<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\MoneyBatch;

use Temporal\Activity\ActivityInterface;

#[ActivityInterface(prefix: "MoneyBatch.")]
interface AccountInterface
{
    public function deposit(string $accountId, string $referenceId, int $amountCents): void;

    public function withdraw(string $accountId, string $referenceId, int $amountCents): void;
}