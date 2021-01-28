<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temporal\Samples\FileProcessing;

class TaskQueueFilenamePair
{
    public string $hostTaskQueue;
    public string $filename;

    public function __construct(string $hostTaskQueue, string $filename)
    {
        $this->hostTaskQueue = $hostTaskQueue;
        $this->filename = $filename;
    }
}