<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temporal\Samples\FileProcessing;

use Psr\Log\LoggerInterface;
use Temporal\SampleUtils\Logger;

class StoreActivity implements StoreActivitiesInterface
{
    private static string $taskQueue;
    private LoggerInterface $logger;

    public function __construct(string $taskQueue = FileProcessingWorkflow::DEFAULT_TASK_QUEUE)
    {
        self::$taskQueue = $taskQueue;
        $this->logger = new Logger();
    }

    public function upload(string $localFileName, string $url): void
    {
        if (!is_file($localFileName)) {
            throw new \InvalidArgumentException("Invalid file type: " . $localFileName);
        }

        // Faking upload to simplify sample implementation.
        $this->log('upload activity: uploaded from %s to %s', $localFileName, $url);
    }

    public function process(string $inputFileName): string
    {
        try {
            $this->log('process activity: sourceFile=%s', $inputFileName);
            $processedFile = $this->processFile($inputFileName);
            $this->log('process activity: processed file=%s', $processedFile);

            return $processedFile;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function download(string $url): TaskQueueFilenamePair
    {
        try {
            $this->log('download activity: downloading %s', $url);

            $data = file_get_contents($url);
            $file = tempnam(sys_get_temp_dir(), 'demo');

            file_put_contents($file, $data);

            $this->log('download activity: downloaded from %s to %s', $url, realpath($file));

            return new TaskQueueFilenamePair(self::$taskQueue, $file);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function processFile(string $filename): string
    {
        // faking processing for simplicity
        return $filename;
    }

    /**
     * @param string $message
     * @param mixed ...$arg
     */
    private function log(string $message, ...$arg)
    {
        // by default all error logs are forwarded to the application server log and docker log
        $this->logger->debug(sprintf($message, ...$arg));
    }
}