<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Lambda;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    public const TASK_QUEUE = 'php-lambda';

    protected const NAME = 'lambda';
    protected const DESCRIPTION = 'Execute Lambda\GreetingWorkflow on a worker running in AWS Lambda';

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            GreetingWorkflowInterface::class,
            WorkflowOptions::new()
                ->withTaskQueue(self::TASK_QUEUE)
                ->withWorkflowExecutionTimeout(CarbonInterval::minutes(5)),
        );

        $output->writeln('Starting <comment>GreetingWorkflow</comment> on the <comment>' . self::TASK_QUEUE . '</comment> task queue... ');

        $run = $this->workflowClient->start($workflow, 'Antony');

        $output->writeln(
            \sprintf('Started: WorkflowID=<fg=magenta>%s</fg=magenta>', $run->getExecution()->getID()),
        );
        $output->writeln('The workflow sleeps for 30 seconds, so it outlives a single Lambda invocation.');
        $output->writeln('Keep invoking the function until it completes.');

        $output->writeln(\sprintf("Result:\n<info>%s</info>", $run->getResult()));

        return self::SUCCESS;
    }
}
