<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Updates;

use Exception;
use React\Promise\PromiseInterface;
use Temporal\Promise;
use Temporal\Workflow;

/**
 * Demonstrates asynchronous signalling of a workflow. Requires a local instance of Temporal server
 * to be running.
 */
class UpdateWorkflow implements UpdateWorkflowInterface
{
    // Rules
    private const DICES_COUNT = 6;
    private const SCORE_STRAIGHT = 1500;
    private const SCORE_THREE_PAIRS = 750;
    private const SCORE_SEQUENCES = [
        1 => 1000,
        2 => 200,
        3 => 300,
        4 => 400,
        5 => 500,
        6 => 600,
    ];

    private bool $exit = false;
    private State $state;

    public function handle(int $maxTries = 3)
    {
        $this->state = new State();

        yield $this->resetDices();

        yield Workflow::await(fn() => $this->exit);

        return $this->state;
    }

    public function roll()
    {
        --$this->state->tries;
        yield $this->rollDices();
        $this->endIfPossible();
        return $this->state;
    }

    /**
     * The method validates the roll action is possible in the current game state.
     *
     * @throws Exception
     */
    public function validateRoll(): void
    {
        $this->state->ended and throw new \Exception('Game ended');
        $this->state->tries > 0 or throw new \Exception('No more tries');
        // Unreachable condition
        $this->state->dices !== [] or throw new \Exception('You forgot your dices');
    }

    public function holdAndRoll(array $colors)
    {
        // Take dices
        $dices = $this->takeDices($colors, true);
        // Calculate score
        $score = $this->calcDicesScore($dices);
        $this->state->score += $score;

        if ($this->state->dices === []) {
            // Reset dices if there are no dices left
            yield $this->resetDices();
        } else {
            // Roll the remaining dices
            yield $this->rollDices();
        }

        // Check if the game ended
        $this->endIfPossible();

        return $this->state;
    }

    /**
     * Note: validation method must have the same signature as the update method.
     * @throws Exception
     */
    public function validateHoldAndRoll(array $colors): void
    {
        $this->state->ended and throw new \Exception('Game ended');
        $colors === [] and throw new \Exception('You must pick at least one dice');
        \count($colors) <= \count($this->state->dices) or throw new \Exception('Invalid dices count');
        \array_unique($colors) === $colors or throw new \Exception('You can not use the same dice twice');
        // Picked dices are available
        $dices = $this->takeDices($colors);
        // Scores are calculated here
        $this->calcDicesScore($dices);
    }

    public function complete()
    {
        $this->hasPossibleMoves() or --$this->state->tries;

        $this->state->ended = true;
        $this->exit();
        return $this->state;
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function exit()
    {
        $this->exit = true;
    }

    /**
     * Take dices by colors
     *
     * @param array<non-empty-string> $colors
     * @param bool $remove Remove dices from the table
     * @return list<Dice>
     * @throws \Exception
     */
    private function takeDices(array $colors, bool $remove = false): array
    {
        $picked = [];
        foreach ($colors as $color) {
            foreach ($this->state->dices as $pos => $dice) {
                if ($dice->color === $color) {
                    $picked[] = $dice;
                    if ($remove) {
                        unset($this->state->dices[$pos]);
                    }

                    continue 2;
                }
            }

            throw new \Exception("Dice with color $color not found");
        }

        return $picked;
    }

    /**
     * @param non-empty-list<Dice> $dices
     * @param bool $throwException Throw exception if invalid dice combination
     * @return int<0, max>
     * @throws \Exception
     */
    private function calcDicesScore(array $dices, bool $throwException = true): int
    {
        // Normalize dices values
        $values = \array_map(static fn(Dice $dice): int => $dice->getValue(), $dices);
        \sort($values);

        // Find combinations

        if (\count($values) === 6) {
            // Straight
            if ($values === [1, 2, 3, 4, 5, 6]) {
                return self::SCORE_STRAIGHT;
            }

            // Three pairs
            if (\count(\array_unique($values)) === 3
                && $values[0] === $values[1] && $values[2] === $values[3] && $values[4] === $values[5]
            ) {
                return self::SCORE_THREE_PAIRS;
            }
        }

        $score = 0;
        // Find sequences
        $counts = \array_count_values($values);
        foreach ($counts as $value => $count) {
            if ($count >= 3) {
                $score += self::SCORE_SEQUENCES[$value] * ($count - 2);
                unset($counts[$value]);
                continue;
            }

            // Dices 1 and 5 have special score outside of sequences
            $score += match ($value) {
                1 => 100 * $count,
                5 => 50 * $count,
                default => $throwException
                    ? throw new \Exception("Picked dice with value $value has no score")
                    : 0,
            };
        }

        return $score;
    }

    private function rollDices(): PromiseInterface
    {
        $dices = $this->state->dices;
        $promises = $this->state->dices = [];
        foreach ($dices as $dice) {
            // Might be replaced with an activity call
            $promises[] = Workflow::sideEffect(static function () use ($dice): Dice {
                $dice->reRoll();
                return $dice;
            })->then(
                function (Dice $dice): Dice {
                    $this->state->dices[] = $dice;
                    return $dice;
                }
            );
        }

        return Promise::all($promises);
    }

    /**
     * Check possible moves and end the game if there are no moves left
     *
     * @return bool True if the game ended
     */
    private function endIfPossible(): bool
    {
        if ($this->state->tries > 0) {
            return false;
        }

        if ($this->hasPossibleMoves()) {
            return false;
        }

        $this->state->score = 0;
        $this->state->ended = true;

        return true;
    }

    private function hasPossibleMoves(): bool
    {
        return $this->calcDicesScore($this->state->dices, false) > 0;
    }

    /**
     * Remove all dices from the table and create new ones
     */
    private function resetDices(): PromiseInterface
    {
        $this->state->dices = [];
        for ($i = 0; $i < self::DICES_COUNT; $i++) {
            $this->state->dices[] = new Dice($i);
        }

        return $this->rollDices();
    }
}