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
use Temporal\Common\IdReusePolicy;
use Temporal\Exception\Client\WorkflowExecutionAlreadyStartedException;
use Temporal\SampleUtils\Command;

class SubscribeCommand extends Command
{
    protected const NAME = 'subscribe:start';
    protected const DESCRIPTION = 'Execute Subscription\SubscriptionWorkflow with custom user ID';

    protected const ARGUMENTS = [
        ['userID', InputArgument::REQUIRED, 'Unique user ID']
    ];

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $userID = $input->getArgument('userID');

        $workflow = $this->workflowClient->newWorkflowStub(
            SubscriptionWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowId('subscription:' . $userID)
                ->withWorkflowIdReusePolicy(IdReusePolicy::POLICY_ALLOW_DUPLICATE)
        );

        $output->writeln("Start <comment>SubscriptionWorkflow</comment>... ");

        try {
            $run = $this->workflowClient->start($workflow, $userID);
        } catch (WorkflowExecutionAlreadyStartedException $e) {
            $output->writeln('<fg=red>Already running</fg=red>');
            return self::SUCCESS;
        }

        $output->writeln(
            sprintf(
                'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                $run->getExecution()->getID(),
                $run->getExecution()->getRunID(),
            )
        );

        return self::SUCCESS;
    }
}