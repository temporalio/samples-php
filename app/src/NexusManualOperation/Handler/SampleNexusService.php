<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusManualOperation\Handler;

use Temporal\Nexus\Attribute\AsyncOperation;
use Temporal\Nexus\Attribute\Service;
use Temporal\Samples\NexusManualOperation\Service\JobInput;
use Temporal\Samples\NexusManualOperation\Service\JobResult;

#[Service]
final class SampleNexusService
{
    public function __construct(
        private readonly ExternalJobClient $client = new ExternalJobClient(),
    ) {}

    #[AsyncOperation(output: JobResult::class, input: JobInput::class)]
    public function startJob(): JobOperationHandler
    {
        return new JobOperationHandler($this->client);
    }
}
