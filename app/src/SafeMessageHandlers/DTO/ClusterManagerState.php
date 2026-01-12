<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

final class ClusterManagerState
{
    public bool $clusterStarted = false;
    public bool $clusterShutdown = false;
    /** @var array<string, string|null> A [Node => Job Name] array*/
    public array $nodes = [];
    /** @var array<string> */
    public array $jobsAssigned = [];
}
