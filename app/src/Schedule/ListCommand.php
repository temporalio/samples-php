<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Schedule;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\SampleUtils\Command;

class ListCommand extends Command
{
    protected const NAME = 'schedule:list';
    protected const DESCRIPTION = 'List all Schedules';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $schedules = $this->scheduleClient->listSchedules();

        if (\count($schedules->getPageItems()) === 0) {
            $output->writeln('<fg=red>No Schedules found</fg=red>');
            return self::FAILURE;
        }

        $rows = [];
        foreach ($schedules as $schedule) {
            $previous = empty($schedule->info->recentActions)
                ? null
                : $schedule->info->recentActions[\array_key_last($schedule->info->recentActions)];
            $rows[] = [
                $schedule->scheduleId,
                $schedule->info->workflowType->name,
                \number_format((float)$schedule->info->futureActionTimes[0]
                    ->diff(new \DateTimeImmutable())->format('%s.%F'), 3) . 's',
                $previous === null
                    ? 'N/A'
                    : \number_format((float)$previous->actualTime
                        ->diff(new \DateTimeImmutable())->format('%s.%F'), 3) . 's',
            ];
        }

        (new Table($output))
            ->setHeaderTitle('Recent Actions')
            ->setHeaders(['ID', 'WF Type', 'Next run in', 'Last run'])
            ->setRows($rows)
            ->render();

        return self::SUCCESS;
    }
}