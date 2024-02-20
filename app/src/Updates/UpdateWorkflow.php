<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Updates;

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

    /**
     * Available dices
     * @var array<Dice>
     */
    private array $dices = [];

    private int $tries = 1;
    private bool $ended = false;
    private bool $exit = false;
    private int $score = 0;

    /**
     * @return int<0, max> Score
     */
    public function handle(int $maxTries = 3)
    {
        for ($i = 0; $i < self::DICES_COUNT; $i++) {
            $this->dices[] = new Dice($i);
        }

        yield Workflow::await(fn() => $this->exit);
    }

    public function roll()
    {
        --$this->tries;
        yield $this->rollDices();
        return $this->dices;
    }

    public function validateRoll(): void
    {
        $this->ended and throw new \Exception('Game ended');
        $this->tries > 0 or throw new \Exception('No more tries');
    }

    public function holdAndRoll(array $colors)
    {
        // Picked dices
        $dices = $this->pickDices($colors);
        // Calculate score
        $score = $this->calcDicesScore($dices);
        $this->score += $score;
        // Remove picked dices
        foreach ($this->dices as $key => $dice) {
            if (\in_array($dice, $dices, true)) {
                unset($this->dices[$key]);
            }
        }
        // Roll the remaining dices
        yield $this->rollDices();

        // todo Check if the game ended

        // todo Check if there are no dices left
    }

    public function validateHoldAndRoll(array $colors): void
    {
        $this->ended and throw new \Exception('Game ended');
        $colors === [] and throw new \Exception('You must pick at least one dice');
        \count($colors) <= \count($this->dices) or throw new \Exception('Invalid dices count');
        \array_unique($colors) === $colors or throw new \Exception('You can not use the same dice twice');
        // Picked dices are available
        $dices = $this->pickDices($colors);
        // Scores are calculated here
        $this->calcDicesScore($dices);
    }

    public function complete()
    {
        // TODO: Implement complete() method.
    }

    /**
     * @param array<non-empty-string> $colors
     * @return list<Dice>
     * @throws \Exception
     */
    private function pickDices(array $colors): array
    {
        $picked = [];
        foreach ($colors as $color) {
            foreach ($this->dices as $dice) {
                if ($dice->color === $color) {
                    $picked[] = $dice;
                    continue 2;
                }
            }

            throw new \Exception("Dice with color $color not found");
        }

        return $picked;
    }

    /**
     * @param non-empty-list<Dice> $dices
     * @return int<0, max>
     * @throws \Exception
     */
    private function calcDicesScore(array $dices): int
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
                $score += self::SCORE_SEQUENCES[$value] ** ($count - 2);
                unset($counts[$value]);
                continue;
            }

            // Dices 1 and 5 have special score outside of sequences
            $score += match ($value) {
                1 => 100 * $count,
                5 => 50 * $count,
                default => throw new \Exception("Picked dice with value $value has no score"),
            };
        }

        return $score;
    }

    private function rollDices(): PromiseInterface
    {
        $dices = $this->dices;
        $promises = $this->dices = [];
        foreach ($dices as $dice) {
            // Might be replaced with an activity call
            $promises[] = Workflow::sideEffect(static function () use ($dice): Dice {
                $dice->reRoll();
                return $dice;
            })->then(
                function (Dice $dice): Dice {
                    $this->dices[] = $dice;
                    return $dice;
                }
            );
        }

        return Promise::all($promises);
    }
}