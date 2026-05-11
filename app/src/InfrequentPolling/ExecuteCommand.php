<?php

declare(strict_types=1);

namespace Temporal\Samples\InfrequentPolling;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'infrequent-polling';
    protected const DESCRIPTION = 'Execute InfrequentPolling\GreetingWorkflow';

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            GreetingWorkflowInterface::class,
            WorkflowOptions::new()->withWorkflowExecutionTimeout(CarbonInterval::minutes(10))
        );

        $output->writeln("Starting <comment>InfrequentPolling\\GreetingWorkflow</comment>... ");

        $run = $this->workflowClient->start($workflow, 'World');

        $output->writeln(
            sprintf(
                'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                $run->getExecution()->getID(),
                $run->getExecution()->getRunID(),
            )
        );

        $output->writeln(sprintf("Result:\n<info>%s</info>", $run->getResult('string')));

        return self::SUCCESS;
    }
}
