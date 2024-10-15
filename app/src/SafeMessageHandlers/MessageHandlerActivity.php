<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Samples\SafeMessageHandlers\DTO\AssignNodesToJobInput;
use Temporal\Samples\SafeMessageHandlers\DTO\FindBadNodesInput;
use Temporal\Samples\SafeMessageHandlers\DTO\UnassignNodesForJobInput;

#[ActivityInterface]
class MessageHandlerActivity implements MessageHandlerActivityInterface
{
    #[ActivityMethod(name: 'assign_nodes_to_job')]
    public function assignNodesToJob(AssignNodesToJobInput $input): void
    {
        \trap('Assigning nodes ' . \implode(', ', $input->nodes) . ' to job ' . $input->jobName);
        \sleep(1);
    }

    #[ActivityMethod(name: 'unassign_nodes_for_job')]
    public function unassign_nodes_for_job(UnassignNodesForJobInput $input): void
    {
        \trap('Deallocating nodes ' . \implode(', ', $input->nodes) . ' from job ' . $input->jobName);
        \sleep(1);
    }

    /**
     * @return string[]
     */
    #[ActivityMethod(name: 'find_bad_nodes')]
    public function find_bad_nodes(FindBadNodesInput $input): array
    {
        \sleep(1);
        $badNodes = [];
        foreach ($input->nodesToCheck as $node) {
            if ((int) $node % 5 === 0) {
                $badNodes[] = $node;
            }
        }

        \trap(\count($badNodes) > 0
            ? 'Found bad nodes: ' . \implode(', ', $badNodes)
            : 'No new bad nodes found.',
        );

        return $badNodes;
    }
}
