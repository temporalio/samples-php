<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\UpdatableTimer;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Api\Enums\V1\WorkflowIdReusePolicy;
use Temporal\Client\WorkflowOptions;
use Temporal\Exception\Client\WorkflowExecutionAlreadyStartedException;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'updatable-timer:start';
    protected const DESCRIPTION = 'Execute UpdatableTimer\DynamicSleepWorkflow';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            DynamicSleepWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowId(DynamicSleepWorkflow::WORKFLOW_ID)
                ->withWorkflowIdReusePolicy(WorkflowIdReusePolicy::WORKFLOW_ID_REUSE_POLICY_ALLOW_DUPLICATE)
                ->withWorkflowExecutionTimeout(CarbonInterval::minutes(2))
        );

        $output->writeln("Starting <comment>DynamicSleepWorkflow</comment> (sleep for 10 seconds)... ");

        try {
            $run = $this->workflowClient->start($workflow, time() + 10);
            $output->writeln(
                sprintf(
                    'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                    $run->getExecution()->getID(),
                    $run->getExecution()->getRunID(),
                )
            );
        } catch (WorkflowExecutionAlreadyStartedException $e) {
            $output->writeln('<fg=red>Workflow execution already started</fg=red>');
        }

        return self::SUCCESS;
    }
}