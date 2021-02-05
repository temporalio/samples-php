<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\MoneyTransfer;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'money-transfer';
    protected const DESCRIPTION = 'Execute MoneyTransferWorkflow';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workflow = $this->workflowClient->newWorkflowStub(AccountTransferWorkflowInterface::class);
        $output->writeln("Starting <comment>MoneyTransferWorkflow</comment>... ");

        // runs in blocking mode
        $workflow->transfer(
            'fromID',
            'toID',
            'refID',
            1000
        );
                
        $output->writeln("<info>Workflow complete</info>");

        return self::SUCCESS;
    }
}