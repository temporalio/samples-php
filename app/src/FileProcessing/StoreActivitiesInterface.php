<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temporal\Samples\FileProcessing;

use Temporal\Activity\ActivityInterface;

#[ActivityInterface(prefix:"FileProcessing.")]
interface StoreActivitiesInterface
{
    /**
     * Upload file to remote location.
     *
     * @param string $localFileName file to upload
     * @param string $url remote location
     */
    public function upload(string $localFileName, string $url): void;

    /**
     * Process file.
     *
     * @param string $inputFileName source file name @@return processed file name
     * @return string
     */
    public function process(string $inputFileName): string;

    /**
     * Downloads file to local disk.
     *
     * @param string $url remote file location
     * @return TaskQueueFilenamePair local task queue and downloaded file name
     */
    public function download(string $url): TaskQueueFilenamePair;
}