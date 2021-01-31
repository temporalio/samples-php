<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Subscription;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Exception\Failure\CanceledFailure;
use Temporal\Workflow;

class SubscriptionWorkflow implements SubscriptionWorkflowInterface
{
    private $account;
    private \DateInterval $chargePeriod;

    public function __construct()
    {
        // Lower period duration to observe workflow behaviour
        $this->chargePeriod = CarbonInterval::seconds(30);

        $this->account = Workflow::newActivityStub(
            AccountActivityInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(2))
        );
    }

    public function subscribe(string $userID)
    {
        yield $this->account->sendWelcomeEmail($userID);

        try {
            $trialPeriod = true;
            while (true) {
                yield Workflow::timer($this->chargePeriod);
                yield $this->account->chargeMonthlyFee($userID);

                if ($trialPeriod) {
                    yield $this->account->sendEndOfTrialEmail($userID);
                    $trialPeriod = false;
                    continue;
                }

                yield $this->account->sendMonthlyChargeEmail($userID);
            }
        } catch (CanceledFailure $e) {
            yield $this->account->processSubscriptionCancellation($userID);
            yield $this->account->sendSorryToSeeYouGoEmail($userID);
        }
    }
}