<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Schedule;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\Schedule;
use Temporal\Common\RetryOptions;
use Temporal\SampleUtils\Command;

class CreateCommand extends Command
{
    protected const NAME = 'schedule:create';
    protected const DESCRIPTION = 'Schedule a workflow';

    protected const ARGUMENTS = [
        ['name', InputOption::VALUE_REQUIRED, 'Person name', 'John Doe'],
    ];

    protected const OPTIONS = [
        ['language', 'l', InputOption::VALUE_OPTIONAL, 'Language', null],
        ['interval', 'i', InputOption::VALUE_OPTIONAL, 'Schedule interval', null],
        ['cron', 'c', InputOption::VALUE_OPTIONAL, 'Cron schedule', null],
    ];


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $specInit = $spec = Schedule\Spec\ScheduleSpec::new()
            ->withJitter('10m');

        // Parse options
        $inputName = $input->getArgument('name');
        $language = $input->getOption('language') ?? ['en', 'ru', 'ua', 'fr', 'de'][\random_int(0, 4)];
        $interval = $input->getOption('interval');
        $cron = $input->getOption('cron');

        // Set Schedule Spec parameters
        $interval and $spec = $spec->withAddedInterval($interval);
        $cron and $spec = $spec->withAddedCronString($cron);

        if ($specInit === $spec) {
            $output->writeln('<fg=red>Need to specify interval or cron</fg=red>');
            $output->writeln('<fg=green>Use --interval or --cron options</fg=green>');
            return self::FAILURE;
        }

        $handle = $this->scheduleClient->createSchedule(
            Schedule\Schedule::new()->withAction(
                Schedule\Action\StartWorkflowAction::new(ScheduleWorkflowInterface::WORKFLOW_TYPE)
                    ->withInput([$inputName])
                    ->withRetryPolicy(RetryOptions::new()->withMaximumAttempts(3))
                    ->withHeader(['language' => $language])
                    ->withWorkflowId(ScheduleWorkflowInterface::WORKFLOW_ID)
                    ->withWorkflowRunTimeout('10m')
            )->withSpec(
                $spec
            )->withPolicies(Schedule\Policy\SchedulePolicies::new()
                ->withCatchupWindow('10m')
                ->withPauseOnFailure(true)
                ->withOverlapPolicy(Schedule\Policy\ScheduleOverlapPolicy::TerminateOther)
            ),
            scheduleId: ScheduleWorkflowInterface::SCHEDULE_ID,
        );

        $output->writeln(\sprintf(
            'Schedule <info>%s</info> has been created with ID <info>%s</info>',
            ScheduleWorkflowInterface::WORKFLOW_TYPE,
            $handle->getID(),
        ));

        // Collect planned runs
        $plannedRuns = $handle->listScheduleMatchingTimes(
            new \DateTimeImmutable('now'),
            new \DateTimeImmutable('+1 hour'),
        );
        \count($plannedRuns) > 0 and $output->writeln('A few planned runs next hour:');
        $i = 0;
        foreach ($plannedRuns as $time) {
            $output->writeln(\sprintf(
                '  <info>%s</info>',
                $time->format(DATE_ATOM),
            ));
            if (++$i >= 10) {
                break;
            }
        }

        return self::SUCCESS;
    }
}