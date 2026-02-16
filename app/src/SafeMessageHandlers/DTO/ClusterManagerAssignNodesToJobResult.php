<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

final class ClusterManagerAssignNodesToJobResult
{
    /**
     * @param string[] $nodesAssigned
     */
    public function __construct(
        public array $nodesAssigned,
    ) {}
}
