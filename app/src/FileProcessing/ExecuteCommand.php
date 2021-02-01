<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\FileProcessing;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\Samples\Exception\FailedWorkflow;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'process-file';
    protected const DESCRIPTION = 'Execute FileProcessing\FileProcessingWorkflow with local task queue routing';

    protected const ARGUMENTS = [
        ['url', InputArgument::REQUIRED, 'Download URL']
    ];

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            FileProcessingWorkflowInterface::class,
            WorkflowOptions::new()->withWorkflowExecutionTimeout(CarbonInterval::minute(10))
        );

        $output->writeln("Starting <comment>FileProcessing</comment>... ");

        // This is going to block until the workflow completes.
        // This is rarely used in production. Use the commented code below for async start version.
        $result = $workflow->processFile($input->getArgument('url'), 'targetURL');
        $output->writeln(sprintf("Result:\n<info>%s</info>", print_r($result, true)));

        //$run = $this->workflowClient->start($workflow, $input->getArgument('url'), 'targetURL');
        //$output->writeln(
        //    sprintf(
        //        'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
        //        $run->getExecution()->getID(),
        //        $run->getExecution()->getRunID(),
        //    )
        //);
        //$output->writeln(sprintf("Result:\n<info>%s</info>", print_r($run->getResult(), true)));

        return self::SUCCESS;
    }
}