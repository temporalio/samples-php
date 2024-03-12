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
use Temporal\Exception\Failure\TemporalFailure;
use Temporal\Samples\Updates\Zonk\State;
use Temporal\Samples\Updates\Zonk\Table;
use Temporal\SampleUtils\Command;

class ExecuteCommand extends Command
{
    protected const NAME = 'update';
    protected const DESCRIPTION = 'Execute Workflow Update';
    protected const DICES = [
        1 => ['     ', '  •  ', '     '],
        2 => ['  •  ', '     ', '  •  '],
        3 => ['    •', '  •  ', '•    '],
        4 => ['•   •', '     ', '•   •'],
        5 => ['•   •', '  •  ', '•   •'],
        6 => ['•   •', '•   •', '•   •'],
    ];

    private InputInterface $input;
    private OutputInterface $output;

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $workflow = $this->workflowClient->newWorkflowStub(
            UpdateWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowExecutionTimeout(CarbonInterval::day())
        );

        $output->writeln("Starting <comment>UpdateWorkflow</comment>... ");

        $run = $this->workflowClient->start($workflow);

        $output->writeln(
            \sprintf(
                '<options=bold>Zonk</> workflow scheduled: WorkflowID=<fg=magenta>%s</>, RunID=<fg=magenta>%s</>',
                $run->getExecution()->getID(),
                $run->getExecution()->getRunID(),
            ),
        );

        try {
            $state = $workflow->getState();
            do {
                // Wait input
                $state->canRoll and $this->output
                    ->writeln('<fg=gray>Press <options=bold>Enter</options=bold> to roll the dices...</>');
                $state->dices->isEmpty() or $this->output
                    ->writeln('<fg=gray>Choose scoring dices to set aside (e.g. 1 2 3)...</>');
                $state->score  > 0 and $this->output
                    ->writeln(\sprintf(
                        '<fg=gray>Enter "%s" or "%s" to stop the game...</>',
                        '<options=bold>stop</options=bold>',
                        '<options=bold>bank</options=bold>',
                    ));
                $answer = $this->ask('');

                try {
                    if ($answer === null) {
                        $this->printInfo('Rolling the dices...');
                        $state = $workflow->roll();
                        $this->renderDices($state->dices);

                        $state->ended and $this->printDanger('Game over!');
                        continue;
                    }

                    if (\in_array(\strtolower($answer), ['stop', 'bank'], true)) {
                        $state->ended and $this->printInfo('Stopping the game...');
                        $state = $workflow->complete();

                        $this->printInfo(\sprintf(
                            'Turn over! Your score is <options=bold>%d</options=bold>',
                            $state->score,
                        ));

                        continue;
                    }

                    // map number to colors
                    $colors = $this->mapDices($state->dices, $answer);
                    $this->printInfo(\sprintf('Chosen %s', \implode(', ', $colors)));
                    $before = $state->score;
                    $state = $workflow->choose($colors);
                    $this->printInfo(\sprintf(
                        'Your total score is %d (<options=bold>+%d</options=bold>)',
                        $state->score,
                        $state->score - $before,
                    ));
                    $this->renderDices($state->dices);
                } catch (\Throwable $e) {
                    $previous = $e->getPrevious();
                    $output->writeln(\sprintf('<fg=red>%s</>', $previous instanceof TemporalFailure
                        ? $previous->getOriginalMessage()
                        : $previous?->getMessage() ?? $e->getMessage()));
                    $this->ask('<fg=gray>Press <options=bold>Enter</options=bold> to continue...</>');
                    $this->renderState($state);
                    continue;
                }
            } while (!$state->ended);
        } catch (\Throwable $e) {
            $this->output->writeln(\sprintf('<fg=red>%s</>', $e->getMessage()));
            $this->output->writeln(\sprintf('<fg=red>%s</>', $e->getPrevious()?->getMessage()));
        }

        return self::SUCCESS;
    }

    private function renderState(State $state): void
    {
        $this->output->writeln('<options=bold>Game status</>');
        // Render Score
        $this->output->writeln(\sprintf('<fg=green>Score: %d</>', $state->score));

        $this->output->writeln('');
        // Render Dices
        $this->renderDices($state->dices);
    }

    private function renderDices(Table $dices): void
    {
        if ($dices->isEmpty()) {
            return;
        }

        $diceLines = [];
        $numberString = [];
        foreach ($dices as $k => $dice) {
            $matrix = self::DICES[$dice->getValue()];

            $diceLines[0][] = \sprintf('<fg=%s>┌───────┐</>', $dice->color);
            $diceLines[1][] = \sprintf('<fg=%s>│ %s │</>', $dice->color, $matrix[0]);
            $diceLines[2][] = \sprintf('<fg=%s>│ %s │</>', $dice->color, $matrix[1]);
            $diceLines[3][] = \sprintf('<fg=%s>│ %s │</>', $dice->color, $matrix[2]);
            $diceLines[4][] = \sprintf('<fg=%s>└───────┘</>',  $dice->color);
            $numberString[] = \str_pad((string)($k + 1), 9, ' ', STR_PAD_BOTH);
        }

        foreach ($diceLines as $line) {
            $this->output->writeln(\implode(' ', $line));
        }
        $this->output->writeln('<fg=gray>' . \implode(' ', $numberString) . '</>');
    }

    private function ask(string $message): ?string
    {
        return (new QuestionHelper())->ask($this->input, $this->output, new Question($message));
    }

    /**
     * @return list<non-empty-string>
     */
    private function mapDices(Table $dices, string $answer): array
    {
        $result = [];
        \preg_match_all('/\d/', $answer, $matches);
        foreach ($matches[0] as $match) {
            $result[] = $dices->getByIndex((int)$match - 1)->color;
        }

        return $result;
    }

    public function printDanger($text): void
    {
        $this->output->writeln('');
        $this->output->writeln("<bg=red;fg=white;options=bold>$text</>");
        $this->output->writeln('');
    }

    public function printInfo($text): void
    {
        $this->output->writeln("<fg=cyan>$text</>");
    }
}