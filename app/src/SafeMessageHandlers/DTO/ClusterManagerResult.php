<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

final class ClusterManagerResult
{
    public function __construct(
        public int $count,
        public int $count1,
    ) {}
}
