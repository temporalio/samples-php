<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Interceptors;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\Samples\Interceptors\Workflow\TestActivityAttributesInterceptor;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'interceptors';
    protected const DESCRIPTION = 'Execute workflow with interceptors';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            TestActivityAttributesInterceptor::class,
            WorkflowOptions::new()
                ->withTaskQueue('interceptors')
                ->withWorkflowExecutionTimeout(CarbonInterval::minute())
        );

        $output->writeln("Starting <comment>Interceptors.TestActivityAttributesInterceptor</comment>... ");

        $run = $this->workflowClient->start($workflow);

        $output->writeln(
            \sprintf(
                'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                $run->getExecution()->getID(),
                $run->getExecution()->getRunID(),
            ),
        );

        $output->writeln(\sprintf("Result:\n<info>%s</info>", \print_r($run->getResult(), true)));

        return self::SUCCESS;
    }
}