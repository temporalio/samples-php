<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SimpleBatch;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\Exception\Client\WorkflowExecutionAlreadyStartedException;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'simple-batch:start';

    protected const DESCRIPTION = 'Start SimpleBatchWorkflow';

    protected const ARGUMENTS = [
        ['batchId', InputArgument::REQUIRED, 'The batch id'],
    ];

    protected const OPTIONS = [
        ['min', null, InputOption::VALUE_REQUIRED, 'The minimum number of batch items', 10],
        ['max', null, InputOption::VALUE_REQUIRED, 'The maximum number of batch items', 20],
    ];

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchId = intval($input->getArgument('batchId'));
        $options = [
            'min' => intval($input->getOption('min')),
            'max' => intval($input->getOption('max')),
        ];

        $workflow = $this->workflowClient->newWorkflowStub(
            SimpleBatchWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowId(SimpleBatchWorkflowInterface::WORKFLOW_ID . ':' . $batchId)
                ->withWorkflowExecutionTimeout(CarbonInterval::week())
        );

        $output->writeln("Starting <comment>SimpleBatchWorkflow</comment>... ");

        try {
            $run = $this->workflowClient->start($workflow, $batchId, $options);
            $output->writeln(
                sprintf(
                    'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                    $run->getExecution()->getID(),
                    $run->getExecution()->getRunID(),
                )
            );
        } catch (WorkflowExecutionAlreadyStartedException $e) {
            $output->writeln('<fg=red>Still running</fg=red>');
        }

        return self::SUCCESS;
    }
}
