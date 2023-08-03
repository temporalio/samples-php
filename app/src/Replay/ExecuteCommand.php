<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Replay;

use DateTimeImmutable;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\SampleUtils\Command;
use Temporal\Testing\Replay\Exception\ReplayerException;
use Temporal\Testing\Replay\WorkflowReplayer;

class ExecuteCommand extends Command
{
    protected const NAME = 'replay';
    protected const ARGUMENTS = [
        ['workflow-type', InputOption::VALUE_REQUIRED, 'Workflow type to replay', null],
    ];
    protected const OPTIONS = [
        ['time', 't', InputOption::VALUE_OPTIONAL, 'Time back in minutes', 30],
    ];
    protected const DESCRIPTION = 'Replay workflow executions from history events';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $replayer = new WorkflowReplayer();
        $workflowType = $input->getArgument('workflow-type');
        $timeBack = abs((int)$input->getOption('time'));

        $time = new DateTimeImmutable("-$timeBack minutes");
        $output->writeln(
            \sprintf(
                "Replaying <info>%s</info> workflows that were closed last <info>%d</info> minutes...",
                $workflowType ?? 'all',
                $timeBack,
            )
        );

        // find workflows that were closed after $time
        $workflows = $this->workflowClient
            ->listWorkflowExecutions(\sprintf("%s CloseTime > '%s'",
                $workflowType === null ? '' : "WorkflowType='{$workflowType}' AND ",
                $time->format(DATE_ATOM),
            ));

        $output->writeln(\sprintf("Found <info>%s</info>", \count($workflows)));

        foreach ($workflows as $workflow) {
            $output->write(
                sprintf(
                    "Replaying <fg=cyan>%s</> <info>%s::%s</info>... ",
                    $workflow->type->name,
                    $workflow->execution->getID(),
                    $workflow->execution->getRunID(),
                )
            );

            try {
                // Replay workflow
                $replayer->replayFromServer(
                    workflowType: $workflow->type->name,
                    execution: $workflow->execution,
                );
                $output->writeln('<info>[OK]</info>');
            } catch (ReplayerException $e) {
                $output->writeln('<error>[FAILED]</error>');
                $output->writeln(\sprintf('<yellow>%s</yellow>', $e::class));
                $output->writeln("<gray>{$e->getMessage()}</gray>");
            }
        }

        return self::SUCCESS;
    }
}