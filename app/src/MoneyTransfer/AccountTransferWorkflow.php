<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\MoneyTransfer;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Workflow;

class AccountTransferWorkflow implements AccountTransferWorkflowInterface
{
    /** @var AccountInterface */
    private $account;

    public function __construct()
    {
        $this->account = Workflow::newActivityStub(
            AccountInterface::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::seconds(5))
                ->withRetryOptions(RetryOptions::new()->withMaximumAttempts(10))
        );
    }

    public function transfer(string $fromAccountId, string $toAccountId, string $referenceId, int $amountCents)
    {
        yield $this->account->withdraw($fromAccountId, $referenceId, $amountCents);
        yield $this->account->deposit($toAccountId, $referenceId, $amountCents);
    }
}