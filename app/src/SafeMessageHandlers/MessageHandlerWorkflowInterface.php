<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers;

use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerAssignNodesToJobInput;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerAssignNodesToJobResult;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerDeleteJobInput;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerInput;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerState;
use Temporal\Workflow;

#[Workflow\WorkflowInterface]
interface MessageHandlerWorkflowInterface
{
    #[Workflow\WorkflowMethod]
    public function run(ClusterManagerInput $input);

    #[Workflow\SignalMethod('start_cluster')]
    public function startCluster(): void;

    #[Workflow\SignalMethod('shutdown_cluster')]
    public function shutdownCluster();

    /**
     * This is an update as opposed to a signal because the client may want to wait for nodes to be allocated
     * before sending work to those nodes.
     * Returns the list of node names that were allocated to the job.
     */
    #[Workflow\UpdateMethod('assign_nodes_to_job')]
    #[Workflow\ReturnType(ClusterManagerAssignNodesToJobResult::class)]
    public function assignNodesToJob(ClusterManagerAssignNodesToJobInput $input);

    /**
     * Even though it returns nothing, this is an update because the client may want to track it, for example
     * to wait for nodes to be unassigned before reassigning them.
     */
    #[Workflow\UpdateMethod('delete_job')]
    public function deleteJob(ClusterManagerDeleteJobInput $input): \Generator;

    #[Workflow\QueryMethod('get_state')]
    public function getState(): ClusterManagerState;
}