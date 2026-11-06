<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\StandaloneActivity;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\Activity\StartActivityOptions;
use Temporal\SampleUtils\Command;
use Temporal\Worker\WorkerFactoryInterface;

class ExecuteCommand extends Command
{
    protected const NAME = 'standalone-activity';
    protected const DESCRIPTION = 'Execute StandaloneActivity\GreetingActivity without a Workflow';

    private const ACTIVITY_ID = 'standalone_activity_helloworld_ActivityID';
    private const QUERY = "TaskQueue = '" . WorkerFactoryInterface::DEFAULT_TASK_QUEUE . "'";

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $handle = $this->activityClient->start(
            GreetingActivityInterface::TYPE,
            StartActivityOptions::new()
                ->withId(self::ACTIVITY_ID)
                ->withTaskQueue(WorkerFactoryInterface::DEFAULT_TASK_QUEUE)
                ->withScheduleToCloseTimeout(10),
            'Temporal',
        );

        $output->writeln(\sprintf(
            'Started: ActivityID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
            $handle->getID(),
            $handle->getRunID(),
        ));

        $output->writeln(\sprintf("Result:\n<info>%s</info>", $handle->getResult('string')));

        $output->writeln('Activities:');
        foreach ($this->activityClient->listExecutions(self::QUERY) as $info) {
            $output->writeln(\sprintf(
                "\tActivityID: %s, Type: %s, Status: %s",
                $info->activityId,
                $info->activityType,
                $info->status->name,
            ));
        }

        $output->writeln(\sprintf('Total activities: %d', $this->activityClient->countExecutions(self::QUERY)->count));

        return self::SUCCESS;
    }
}
