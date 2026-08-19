<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Nexus;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\ClientOptions as TemporalClientOptions;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowOptions;
use Temporal\Samples\Nexus\Caller\CallerWorker;
use Temporal\Samples\Nexus\Caller\EchoCallerWorkflow;
use Temporal\Samples\Nexus\Caller\HelloCallerWorkflow;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'nexus';
    protected const DESCRIPTION = 'Execute Nexus\\EchoCallerWorkflow + HelloCallerWorkflow (cross-namespace via Nexus)';

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = \getenv('TEMPORAL_ADDRESS') ?: \getenv('TEMPORAL_CLI_ADDRESS') ?: 'localhost:7233';
        $client = WorkflowClient::create(
            ServiceClient::create($host),
            (new TemporalClientOptions())->withNamespace('my-caller-namespace'),
        );

        $output->writeln("Starting <comment>EchoCallerWorkflow</comment>...");
        $echoWorkflow = $client->newWorkflowStub(
            EchoCallerWorkflow::class,
            WorkflowOptions::new()->withTaskQueue(CallerWorker::TASK_QUEUE),
        );
        $run = $client->start($echoWorkflow, 'Nexus Echo 👋');
        $execution = $run->getExecution();
        $output->writeln(\sprintf(
            'Started: WorkflowID=<fg=magenta>%s</> RunID=<fg=magenta>%s</>',
            $execution->getID(),
            $execution->getRunID(),
        ));
        $output->writeln(\sprintf("Result: <info>%s</info>", $run->getResult('string')));

        $output->writeln("\nStarting <comment>HelloCallerWorkflow</comment>...");
        $helloWorkflow = $client->newWorkflowStub(
            HelloCallerWorkflow::class,
            WorkflowOptions::new()->withTaskQueue(CallerWorker::TASK_QUEUE),
        );
        $run = $client->start($helloWorkflow, 'Nexus', Language::ES);
        $execution = $run->getExecution();
        $output->writeln(\sprintf(
            'Started: WorkflowID=<fg=magenta>%s</> RunID=<fg=magenta>%s</>',
            $execution->getID(),
            $execution->getRunID(),
        ));
        $output->writeln(\sprintf("Result: <info>%s</info>", $run->getResult('string')));

        return self::SUCCESS;
    }
}
