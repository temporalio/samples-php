<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\MoneyBatch;

use Temporal\Workflow\QueryMethod;
use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface MoneyBatchWorkflowInterface
{
    #[WorkflowMethod(name: "MoneyBatch")]
    public function deposit(
        string $toAccountId,
        int $batchSize
    );

    #[SignalMethod]
    public function withdraw(
        string $fromAccountId,
        string $referenceId,
        int $amountCents
    );

    #[QueryMethod]
    public function getBalance(): int;

    #[QueryMethod]
    public function getCount(): int;
}