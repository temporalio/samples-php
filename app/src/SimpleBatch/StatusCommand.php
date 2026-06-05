<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SimpleBatch;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\SampleUtils\Command;

class StatusCommand extends Command
{
    protected const NAME = 'simple-batch:status';

    protected const DESCRIPTION = 'Get SimpleBatchWorkflow status';

    protected const ARGUMENTS = [
        ['batchId', InputArgument::REQUIRED, 'The batch id'],
    ];

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchId = intval($input->getArgument('batchId'));

        /** @var SimpleBatchWorkflowInterface */
        $workflow = $this->workflowClient->newRunningWorkflowStub(
            SimpleBatchWorkflowInterface::class,
            SimpleBatchWorkflowInterface::WORKFLOW_ID . ':' . $batchId
        );

        $results = $workflow->getAvailableResults();
        $pending = $workflow->getPendingTasks();
        $failedCount = count(array_filter($results, fn(array $result) => !$result['success']));

        $output->writeln("<info>SimpleBatchWorkflow (id $batchId) status</info>");
        $output->writeln(json_encode([
            'count' => [
                'pending' => count($pending),
                'ended' => count($results),
                'succeeded' => count($results) - $failedCount,
                'failed' => $failedCount,
            ],
            'tasks' => [
                'pending' => $pending,
                'results' => $results,
            ],
        ], JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
