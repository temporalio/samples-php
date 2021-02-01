<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\MoneyBatch;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'money-batch';
    protected const DESCRIPTION = 'Execute MoneyBatch\MoneyBatchWorkflow with multiple signals and queries';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            MoneyBatchWorkflowInterface::class,
            WorkflowOptions::new()->withWorkflowExecutionTimeout(CarbonInterval::minute())
        );

        $output->writeln("Starting <comment>MoneyBatch</comment>... ");

        $run = $this->workflowClient->start($workflow, 'toAccountID', 3);

        $output->writeln(
            sprintf(
                'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                $run->getExecution()->getID(),
                $run->getExecution()->getRunID(),
            )
        );

        $output->writeln(sprintf("Withdraw from <info>%s</info>", 'Bob'));
        $workflow->withdraw('Bob', 'BobRefID', 1000);

        $output->writeln(sprintf("Withdraw from <info>%s</info>", 'John'));
        $workflow->withdraw('John', 'JohnRefID', 21000);

        $output->writeln(sprintf("Withdraw from <info>%s</info>", 'Antony'));
        $workflow->withdraw('Antony', 'AntonyRefID', 173000);

        // wait for completion
        $run->getResult();

        $output->writeln('<info>Complete!</info>');
        $output->writeln(
            sprintf(
                "Final balance: <info>%s</info>, count <info>%s</info>",
                $workflow->getBalance(),
                $workflow->getCount()
            )
        );

        return self::SUCCESS;
    }
}