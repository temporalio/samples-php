<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

final class AssignNodesToJobInput
{
    /**
     * @param string[] $nodes
     */
    public function __construct(
        public array $nodes,
        public string $jobName,
    ) {}
}
