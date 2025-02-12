<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

final class FindBadNodesInput
{
    /**
     * @param array $nodesToCheck
     */
    public function __construct(
        public array $nodesToCheck,
    ) {}
}
