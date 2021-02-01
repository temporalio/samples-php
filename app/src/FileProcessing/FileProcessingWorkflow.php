<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temporal\Samples\FileProcessing;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Workflow;

class FileProcessingWorkflow implements FileProcessingWorkflowInterface
{
    public const DEFAULT_TASK_QUEUE = 'default';

    /** @var ActivityProxy|StoreActivitiesInterface */
    private $defaultStoreActivities;

    public function __construct()
    {
        $this->defaultStoreActivities = Workflow::newActivityStub(
            StoreActivitiesInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::minute(5))
                ->withTaskQueue(self::DEFAULT_TASK_QUEUE)
        );
    }

    public function processFile(string $sourceURL, string $destinationURL)
    {
        /** @var TaskQueueFilenamePair $downloaded */
        $downloaded = yield $this->defaultStoreActivities->download($sourceURL);

        $hostSpecificStore = Workflow::newActivityStub(
            StoreActivitiesInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::minute(5))
                ->withTaskQueue($downloaded->hostTaskQueue)
        );

        // Call processFile activity to zip the file.
        // Call the activity to process the file using worker-specific task queue.
        $processed = yield $hostSpecificStore->process($downloaded->filename);

        // Call upload activity to upload the zipped file.
        yield $hostSpecificStore->upload($processed, $destinationURL);

        return 'OK';
    }
}