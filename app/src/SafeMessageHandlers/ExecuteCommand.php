<?php

declare(strict_types=1);

namespace Temporal\Samples\SafeMessageHandlers;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\Common\IdReusePolicy;
use Temporal\Internal\Client\WorkflowProxy;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerAssignNodesToJobInput;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerDeleteJobInput;
use Temporal\Samples\SafeMessageHandlers\DTO\ClusterManagerInput;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'safe-message-handlers';
    protected const DESCRIPTION = 'Execute Safe Message Handlers Workflow';
    protected const WORKFLOW_ID = 'safe-message-handlers';

    protected const ARGUMENTS = [
        ['jobs', InputArgument::OPTIONAL, 'Jobs count', 6],
        ['continue', InputArgument::OPTIONAL, 'Test continue as new', false],
    ];

    private InputInterface $input;
    private OutputInterface $output;

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $jobs = max(1, (int) $input->getArgument('jobs'));
        $testContinueAsNew = (bool) $input->getArgument('continue');
        $delay = 0.5;

        $workflow = $this->workflowClient->newWorkflowStub(
            MessageHandlerWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowId(self::WORKFLOW_ID)
                ->withWorkflowIdReusePolicy(IdReusePolicy::TerminateIfRunning),
        );

        $output->writeln("Starting <comment>MessageHandlerWorkflow</comment>... ");

        $this->workflowClient->start($workflow, new ClusterManagerInput(testContinueAsNew: $testContinueAsNew));
        $this->doClusterLifecycle($workflow, $delay, $jobs);

        return self::SUCCESS;
    }

    private function doClusterLifecycle(
        MessageHandlerWorkflowInterface|WorkflowProxy $workflow,
        float $delay,
        int $jobs = 6,
    ) {
        $untyped = $workflow->__getUntypedStub();
        $workflow->startCluster();

        $this->output->writeln('Assign jobs to nodes...');
        $handle = [];
        for ($i = 0; $i < $jobs; $i++) {
            $handle[] = $untyped->startUpdate(
                'assign_nodes_to_job',
                new ClusterManagerAssignNodesToJobInput(2, "task-$i"),
            );
        }
        // await
        foreach ($handle as $h) {
            $h->getResult();
        }

        $this->output->writeln("Sleeping for $delay second(s)");
        $delay > 0 and \usleep((int) ($delay * 1000000));

        $this->output->writeln('Deleting jobs...');
        $handle = [];
        for ($i = 0; $i < $jobs; $i++) {
            $handle[] = $untyped->startUpdate(
                'delete_job',
                new ClusterManagerDeleteJobInput("task-$i"),
            );
        }
        // await
        foreach ($handle as $h) {
            $h->getResult();
        }

        $workflow->shutdownCluster();
    }
}