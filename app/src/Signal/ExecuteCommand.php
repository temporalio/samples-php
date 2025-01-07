<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Signal;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'signal';
    protected const DESCRIPTION = 'Execute Signal\SignalWorkflow with multiple signals';

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            SignalWorkflowInterface::class,
            WorkflowOptions::new()->withWorkflowExecutionTimeout(CarbonInterval::minute())
        );

        $output->writeln("Starting <comment>SignalWorkflow</comment>... ");

        $run = $this->workflowClient->start($workflow);

        $runningWorkflowStub = $this->workflowClient->newRunningWorkflowStub(
            SignalWorkflowInterface::class,
            $run->getExecution()->getID(),
        );

        $untypedRunningWorkflowStub = $this->workflowClient->newUntypedRunningWorkflowStub(
            $run->getExecution()->getID(),
        );

        $output->writeln(
            sprintf(
                'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                $run->getExecution()->getID(),
                $run->getExecution()->getRunID(),
            )
        );

        $output->writeln(sprintf("Add from workflow stub: <info>%s</info>", 'Name1'));
        $result = $workflow->addName('Name1');
        $output->writeln(sprintf("Result: <info>%s</info>", $result));

        $output->writeln(sprintf("Add from running workflow stub: <info>%s</info>", 'Name2'));
        $result = $runningWorkflowStub->addName('Name2');
        $output->writeln(sprintf("Result: <info>%s</info>", $result));

        $output->writeln(sprintf("Add from untyped running workflow stub: <info>%s</info>", 'Name3'));
        try {
            // FIXME Client error happens here
            $result = $untypedRunningWorkflowStub->update('addName', 'Name3');
            $output->writeln(sprintf("Result: <info>%s</info>", $result->getValue(0)));
        } catch (\Throwable $t) {
            $output->writeln(sprintf("Error: <info>%s</info>", $t->getMessage()));
        }

        $output->writeln('Signal exit');
        $workflow->exit();

        $output->writeln(sprintf("Result:\n<info>%s</info>", print_r($run->getResult(), true)));

        return self::SUCCESS;
    }
}