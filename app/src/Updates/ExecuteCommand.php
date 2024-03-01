<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Updates;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Temporal\Client\WorkflowOptions;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'update';
    protected const DESCRIPTION = 'Execute Workflow Update';

    private InputInterface $input;
    private OutputInterface $output;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $workflow = $this->workflowClient->newWorkflowStub(
            UpdateWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowExecutionTimeout(CarbonInterval::minutes(2))
        );

        $output->writeln("Starting <comment>UpdateWorkflow</comment>... ");

        $run = $this->workflowClient->start($workflow);

        $output->writeln(
            \sprintf(
                '<options=bold>Zonk</> game started: WorkflowID=<fg=magenta>%s</>, RunID=<fg=magenta>%s</>',
                $run->getExecution()->getID(),
                $run->getExecution()->getRunID(),
            ),
        );

        try {
            $output->writeln("<fg=gray>! The first roll</>");
            $state = $workflow->roll();
            do {
                $this->renderState($state);

                // Wait input
                $answer = $this->ask("Choose dices to hold (e.g. 1 2 3) or press enter to roll again...\n");

                try {
                    if ($answer === null) {
                        $state = $workflow->roll();
                        $state->ended and $this->printDanger('Game over!');
                        continue;
                    }

                    if ($answer === 'stop') {
                        $state->ended and $this->printDanger('Stopping the game...');
                        $state = $workflow->complete();
                        continue;
                    }

                    // map number to colors
                    $colors = $this->mapDices($state->dices, $answer);
                    $output->writeln(\sprintf('<fg=gray>! Holding %s</>', \implode(', ', $colors)));
                    $state = $workflow->holdAndRoll($colors);

                    $state->ended and $this->printDanger('Game over');
                } catch (\Throwable $e) {
                    $output->writeln(\sprintf('<fg=yellow>%s</>', $e->getPrevious()?->getMessage() ?? $e->getMessage()));
                    $this->ask('<fg=gray>Press enter to continue...</>');
                    continue;
                }
            } while (!$state->ended);
        } catch (\Throwable $e) {
            $this->output->writeln(\sprintf('<fg=red>%s</>', $e->getMessage()));
            $this->output->writeln(\sprintf('<fg=red>%s</>', $e->getPrevious()?->getMessage()));
        }

        isset($state) and $this->renderState($state);

        return self::SUCCESS;
    }

    private function renderState(State $state): void
    {
        $this->output->writeln('<options=bold>Game status</>');
        // Render Score
        $this->output->writeln(\sprintf('<fg=green>Score: %d</>', $state->score));
        // Render Tries
        $this->output->writeln(\sprintf('<fg=yellow>Tries: %d</>', $state->tries));

        $this->output->writeln('');
        // Render Dices
        $this->renderDices($state->dices);
    }

    /**
     * @param list<Dice> $dices
     */
    private function renderDices(array $dices): void
    {
        if ($dices === []) {
            return;
        }

        $diceString = [];
        $numberString = [];
        foreach ($dices as $k => $dice) {
            $side = ['×', '•', '••', '•••', '::', ':•:', ':::'][$dice->getValue()];
            $diceString[] = \sprintf(
                '<fg=%s>[%s]</>',
                $dice->color,
                $side,
            );
            $numberString[] = \str_pad((string)($k + 1), \mb_strlen($side) + 2, ' ', STR_PAD_BOTH);
        }

        $this->output->writeln(\implode(' ', $diceString));
        $this->output->writeln('<fg=gray>' . \implode(' ', $numberString) . '</>');
    }

    private function ask(string $message): ?string
    {
        $helper = new QuestionHelper();
        $answer = $helper->ask($this->input, $this->output, new Question($message));
        return $answer;
    }

    private function mapDices(array $dices, string $answer): array
    {
        $result = [];
        \preg_match_all('/\d/', $answer, $matches);
        foreach ($matches[0] as $match) {
            $index = (int)$match - 1;
            if (!isset($dices[$index])) {
                throw new \InvalidArgumentException(\sprintf('Dice with index %d not found', $index));
            }
            $result[] = $dices[$index]->color;
        }
        return $result;
    }

    public function printDanger($text): void
    {
        $this->output->writeln('');
        $this->output->writeln("<bg=red;fg=white;options=bold>$text!</>");
        $this->output->writeln('');
    }
}