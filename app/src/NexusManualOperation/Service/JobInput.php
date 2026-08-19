<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusManualOperation\Service;

final class JobInput
{
    public function __construct(
        public readonly string $jobName,
        public readonly bool $instant = false,
    ) {}
}
