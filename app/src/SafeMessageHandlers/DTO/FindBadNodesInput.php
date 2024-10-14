<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

final class FindBadNodesInput
{
    /**
     * @param array $assignedNodes
     */
    public function __construct(
        public array $assignedNodes,
    ) {}
}
