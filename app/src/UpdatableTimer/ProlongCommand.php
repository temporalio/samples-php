<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\UpdatableTimer;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\SampleUtils\Command;

class ProlongCommand extends Command
{
    protected const NAME = 'updatable-timer:prolong';
    protected const DESCRIPTION = 'Prolong the duration of UpdatableTimer\DynamicSleepWorkflow';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workflow = $this->workflowClient->newRunningWorkflowStub(
            DynamicSleepWorkflowInterface::class,
            DynamicSleepWorkflow::WORKFLOW_ID
        );

        $output->writeln("Prolonging <comment>DynamicSleepWorkflow</comment> for 10 seconds... ");
        $workflow->updateWakeUpTime(time() + 10);

        return self::SUCCESS;
    }
}