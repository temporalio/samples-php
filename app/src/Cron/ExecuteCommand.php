<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Cron;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\Exception\Client\WorkflowExecutionAlreadyStartedException;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'cron';
    protected const DESCRIPTION = 'Start Cron\CronWorkflow';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Sets the cron schedule using the WorkflowOptions.
        // The cron format is parsed by "https://github.com/robfig/cron" library.
        // Besides the standard "* * * * *" format it supports @every and other extensions.
        // Note that unit testing framework doesn't support the extensions.
        // Use single fixed ID to ensure that there is at most one instance running. To run multiple
        // instances set different IDs.

        $workflow = $this->workflowClient->newWorkflowStub(
            CronWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowId(CronWorkflowInterface::WORKFLOW_ID)
                ->withCronSchedule('* * * * *')
                // Execution timeout limits total time. Cron will stop executing after this timeout.
                ->withWorkflowExecutionTimeout(CarbonInterval::minutes(10))
                // Run timeout limits duration of a single workflow invocation.
                ->withWorkflowRunTimeout(CarbonInterval::minute(1))
        );

        $output->writeln("Starting <comment>CronWorkflow</comment>... ");

        try {
            $run = $this->workflowClient->start($workflow, 'Antony');

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