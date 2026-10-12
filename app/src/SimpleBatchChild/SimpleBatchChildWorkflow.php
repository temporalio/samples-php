<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SimpleBatchChild;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Workflow;

class SimpleBatchChildWorkflow implements SimpleBatchChildWorkflowInterface
{
    /**
     * @var SimpleBatchActivityInterface
     */
    private $batchActivity;

    public function __construct()
    {
        $this->batchActivity = Workflow::newActivityStub(
            SimpleBatchActivityInterface::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::seconds(10))
                ->withScheduleToStartTimeout(CarbonInterval::seconds(10))
                ->withScheduleToCloseTimeout(CarbonInterval::minutes(30))
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withMaximumAttempts(100)
                        ->withInitialInterval(CarbonInterval::second(10))
                        ->withMaximumInterval(CarbonInterval::seconds(100))
                )
        );
    }

    /**
     * @inheritDoc
     */
    public function processItem(int $itemId, int $batchId)
    {
        // Set the item processing as started.
        yield $this->batchActivity->itemProcessingStarted($itemId, $batchId);

        // This activity randomly throws an exception.
        $output = yield $this->batchActivity->processItem($itemId, $batchId);

        // Set the item processing as ended.
        yield $this->batchActivity->itemProcessingEnded($itemId, $batchId);

        return $output;
    }
}
