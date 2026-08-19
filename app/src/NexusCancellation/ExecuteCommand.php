<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\NexusCancellation;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\ClientOptions as TemporalClientOptions;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowOptions;
use Temporal\Samples\NexusCancellation\Caller\CallerWorker;
use Temporal\Samples\NexusCancellation\Caller\HelloCallerWorkflow;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'nexus-cancellation';
    protected const DESCRIPTION = 'Fan-out Nexus operations across languages, cancel the rest after the first reply';

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = \getenv('TEMPORAL_ADDRESS') ?: \getenv('TEMPORAL_CLI_ADDRESS') ?: 'localhost:7233';
        $client = WorkflowClient::create(
            ServiceClient::create($host),
            (new TemporalClientOptions())->withNamespace('my-caller-namespace'),
        );

        $output->writeln("Starting <comment>HelloCallerWorkflow</comment>...");
        $workflow = $client->newWorkflowStub(
            HelloCallerWorkflow::class,
            WorkflowOptions::new()->withTaskQueue(CallerWorker::TASK_QUEUE),
        );
        $run = $client->start($workflow, 'Nexus');
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
