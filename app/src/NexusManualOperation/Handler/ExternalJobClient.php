<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusManualOperation\Handler;

final class ExternalJobClient
{
    public function submit(string $jobName, string $requestId): string
    {
        return 'job-' . $requestId;
    }

    public function abort(string $jobId): void
    {
        \error_log("ExternalJobClient: aborted {$jobId}");
    }
}
