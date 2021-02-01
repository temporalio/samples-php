<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Subscription;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\Exception\Client\WorkflowNotFoundException;
use Temporal\SampleUtils\Command;

class CancelCommand extends Command
{
    protected const NAME = 'subscribe:cancel';
    protected const DESCRIPTION = 'Cancel Subscription\SubscriptionWorkflow for user ID';

    protected const ARGUMENTS = [
        ['userID', InputArgument::REQUIRED, 'Unique user ID']
    ];

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $userID = $input->getArgument('userID');

        $workflow = $this->workflowClient->newUntypedRunningWorkflowStub('subscription:' . $userID);

        try {
            $workflow->cancel();
            $output->writeln('Cancelled');
        } catch (WorkflowNotFoundException $e) {
            $output->writeln('<fg=red>Already stopped</fg=red>');
        }

        return self::SUCCESS;
    }
}