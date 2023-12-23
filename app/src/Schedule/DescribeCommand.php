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

class DescribeCommand extends Command
{
    protected const NAME = 'schedule:describe';
    protected const DESCRIPTION = 'Describe the Schedule';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $handle = $this->scheduleClient->getHandle(ScheduleWorkflowInterface::SCHEDULE_ID);

        $describe = $handle->describe();

        $output->writeln(
            \sprintf(
                "Schedule <info>%s</info> of type <info>%s</info> was created at <info>%s</info>\n" .
                "The Schedule is <info>%s</info>\n" .
                "Next action is scheduled in <info>%s</info>\n",
                ScheduleWorkflowInterface::SCHEDULE_ID,
                ScheduleWorkflowInterface::WORKFLOW_TYPE,
                $describe->info->createdAt->format(DATE_ATOM),
                $describe->schedule->state->paused ? "paused '{$describe->schedule->state->notes}'" : 'active',
                \number_format(
                    (float)$describe->info->nextActionTimes[0]
                        ->diff(new \DateTimeImmutable())->format('%s.%F'),
                    3
                ) . 's',
            )
        );

        if (\count($describe->info->recentActions) > 0) {
            $rows = [];
            foreach ($describe->info->recentActions as $recent) {
                $result = $this->workflowClient->newUntypedRunningWorkflowStub(
                    workflowID: $recent->startWorkflowResult->getID(),
                    runID: $recent->startWorkflowResult->getRunID(),
                    workflowType: ScheduleWorkflowInterface::WORKFLOW_TYPE,
                )->getResult();

                $rows[] = [
                    $recent->scheduleTime->format('Y.m.d H:i:s.v'),
                    \number_format((float)$recent->actualTime->diff($recent->scheduleTime)->format('%s.%F'), 3) . 's',
                    $recent->startWorkflowResult->getID(),
                    $result,
                ];
            }

            (new Table($output))
                ->setHeaderTitle('Recent Actions')
                ->setHeaders(['Schedule Time', 'Delay', 'Workflow ID', 'Result'])
                ->setRows($rows)
                ->render();
        }

        return self::SUCCESS;
    }
}