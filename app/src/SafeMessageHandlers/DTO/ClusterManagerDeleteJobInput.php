<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

final class ClusterManagerDeleteJobInput
{
    public function __construct(public string $jobName) {}
}
