<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Query;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'query';
    protected const DESCRIPTION = 'Execute Query\QueryWorkflow with additional query and timer';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            QueryWorkflowInterface::class,
            WorkflowOptions::new()->withWorkflowExecutionTimeout(CarbonInterval::minute())
        );

        $output->writeln("Starting <comment>QueryWorkflow</comment>... ");

        $run = $this->workflowClient->start($workflow, 'Antony');

        $output->writeln(
            sprintf(
                'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                $run->getExecution()->getID(),
                $run->getExecution()->getRunID(),
            )
        );

        $output->writeln("Querying <comment>QueryWorkflow->queryGreeting</comment>... ");
        $output->writeln(sprintf("Query result:\n<info>%s</info>", $workflow->queryGreeting()));

        $output->writeln("Sleeping for 2 seconds... ");
        sleep(2);
        $output->writeln(sprintf("Query result:\n<info>%s</info>", $workflow->queryGreeting()));

        // wait for workflow to complete
        $run->getResult();

        return self::SUCCESS;
    }
}