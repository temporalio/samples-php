<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Subscription;

use Temporal\Activity\ActivityInterface;

#[ActivityInterface(prefix: "Subscription.")]
interface AccountActivityInterface
{
    public function sendWelcomeEmail(string $userID): void;

    public function chargeMonthlyFee(string $userID): void;

    public function sendEndOfTrialEmail(string $userID): void;

    public function sendMonthlyChargeEmail(string $userID): void;

    public function sendSorryToSeeYouGoEmail(string $userID): void;

    public function processSubscriptionCancellation(string $userID): void;
}