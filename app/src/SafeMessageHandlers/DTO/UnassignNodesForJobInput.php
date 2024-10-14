<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

final class UnassignNodesForJobInput
{
    /**
     * @param string[] $nodesToUnassign
     */
    public function __construct(
        public array $nodesToUnassign,
        public string $jobName,
    ) {}
}
