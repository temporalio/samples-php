<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

final class ClusterManagerInput {
    public function __construct(
        public ?ClusterManagerState $state = null,
        public bool $testContinueAsNew = false,
    ) {}
}
