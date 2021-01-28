<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SearchAttributes;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'search-attributes';
    protected const DESCRIPTION = 'Execute SearchAttributes\GreetingWorkflow';

    // todo: finish https://github.com/temporalio/samples-java/blob/master/src/main/java/io/temporal/samples/hello/HelloSearchAttributes.java
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // todo: does not work!
        $workflow = $this->workflowClient->newWorkflowStub(
            GreetingWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowExecutionTimeout(CarbonInterval::minute())
                ->withSearchAttributes(
                    [
                        'key' => 'value',
                        'numeric' => 123,
                        //'map' => ['key' => 'value']
                    ]
                )
        );

        $output->writeln("Starting <comment>GreetingWorkflow</comment>... ");

        $run = $this->workflowClient->start($workflow, 'Antony');

        $output->writeln(
            sprintf(
                'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                $run->getExecution()->getID(),
                $run->getExecution()->getRunID(),
            )
        );

        $output->writeln(sprintf("Result:\n<info>%s</info>", print_r($run->getResult(), true)));

        return self::SUCCESS;
    }
}