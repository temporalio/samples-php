<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers\DTO;

/**
 * Be in the habit of storing message inputs and outputs in serializable structures.
 * This makes it easier to add more over time in a backward-compatible way.
 */
final class ClusterManagerAssignNodesToJobInput
{
    public int $totalNumNodes;
    public string $jobName;
}
