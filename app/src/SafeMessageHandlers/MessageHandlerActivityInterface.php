<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Samples\SafeMessageHandlers\DTO\AssignNodesToJobInput;
use Temporal\Samples\SafeMessageHandlers\DTO\FindBadNodesInput;
use Temporal\Samples\SafeMessageHandlers\DTO\UnassignNodesForJobInput;

#[ActivityInterface]
interface MessageHandlerActivityInterface
{
    #[ActivityMethod(name: 'assign_nodes_to_job')]
    public function assignNodesToJob(AssignNodesToJobInput $input): void;

    #[ActivityMethod(name: 'unassign_nodes_for_job')]
    public function unassign_nodes_for_job(UnassignNodesForJobInput $input): void;

    /**
     * @return string[]
     */
    #[ActivityMethod(name: 'find_bad_nodes')]
    public function find_bad_nodes(FindBadNodesInput $input): array;
}
