<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Periodic;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\Exception\Client\WorkflowExecutionAlreadyStartedException;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'periodic:start';
    protected const DESCRIPTION = 'Start Periodic\PeriodicWorkflow';

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            PeriodicWorkflowInterface::class,
            WorkflowOptions::new()->withWorkflowId(PeriodicWorkflowInterface::WORKFLOW_ID)
        );

        $output->writeln("Starting <comment>PeriodicWorkflow</comment>... ");

        try {
            $run = $this->workflowClient->start($workflow, 'World');
            $output->writeln(
                sprintf(
                    'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                    $run->getExecution()->getID(),
                    $run->getExecution()->getRunID(),
                )
            );
        } catch (WorkflowExecutionAlreadyStartedException $e) {
            $output->writeln('<fg=red>Still running</fg=red>');
        }

        return self::SUCCESS;
    }
}