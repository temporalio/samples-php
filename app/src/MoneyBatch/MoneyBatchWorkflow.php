<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\MoneyBatch;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Common\Uuid;
use Temporal\Workflow;

class MoneyBatchWorkflow implements MoneyBatchWorkflowInterface
{
    /** @var AccountInterface */
    private $account;

    private array $references = [];
    private int $balance = 0;
    private int $count = 0;

    public function __construct()
    {
        $this->account = Workflow::newActivityStub(
            AccountInterface::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::seconds(5))
                ->withScheduleToCloseTimeout(CarbonInterval::hour(1))
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withInitialInterval(CarbonInterval::second(1))
                        ->withMaximumInterval(CarbonInterval::second(10))
                )
        );
    }

    public function deposit(string $toAccountId, int $batchSize)
    {
        // wait for the completion of all transfers
        yield Workflow::await(fn() => $this->count === $batchSize);

        $referenceID = yield Workflow::sideEffect(fn() => Uuid::v4());

        yield $this->account->deposit($toAccountId, $referenceID, $this->balance);
    }

    public function withdraw(string $fromAccountId, string $referenceId, int $amountCents)
    {
        if (isset($this->references[$referenceId])) {
            // duplicate
            return;
        }

        yield $this->account->withdraw($fromAccountId, $referenceId, $amountCents);
        $this->balance += $amountCents;
        $this->count++;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}