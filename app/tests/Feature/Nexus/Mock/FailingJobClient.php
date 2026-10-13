<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Mock;

use Temporal\Nexus\Exception\ErrorType;
use Temporal\Nexus\Exception\HandlerException;
use Temporal\Samples\NexusManualOperation\Handler\ExternalJobClient;

class FailingJobClient extends ExternalJobClient
{
    public const FAILURE_MESSAGE = 'external job backend is down';

    public function submit(string $jobName, string $requestId): string
    {
        throw HandlerException::create(ErrorType::BadRequest, self::FAILURE_MESSAGE);
    }
}
