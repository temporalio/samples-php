<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusManualOperation\Service;

final class JobResult
{
    public function __construct(
        public readonly string $message,
    ) {}
}
