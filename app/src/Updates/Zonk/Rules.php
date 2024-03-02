<?php

declare(strict_types=1);

namespace Temporal\Samples\Updates\Zonk;

final class Rules
{
    // Rules
    public const DICES_COUNT = 6;
    public const SCORE_STRAIGHT = 1500;
    public const SCORE_THREE_PAIRS = 750;
    public const SCORE_SEQUENCES = [
        1 => 1000,
        2 => 200,
        3 => 300,
        4 => 400,
        5 => 500,
        6 => 600,
    ];

    /**
     * @param non-empty-list<Dice> $dices
     * @param bool $throwException Throw exception if invalid dice combination
     * @return int<0, max>
     * @throws \Exception
     */
    public static function calcDicesScore(array $dices, bool $throwException = true): int
    {
        // Normalize dices values
        $values = \array_map(static fn(Dice $dice): int => $dice->getValue(), $dices);
        \sort($values);

        // Find combinations

        if (\count($values) === 6) {
            // Straight
            if ($values === [1, 2, 3, 4, 5, 6]) {
                return Rules::SCORE_STRAIGHT;
            }

            // Three pairs
            if (\count(\array_unique($values)) === 3
                && $values[0] === $values[1] && $values[2] === $values[3] && $values[4] === $values[5]
            ) {
                return Rules::SCORE_THREE_PAIRS;
            }
        }

        $score = 0;
        // Find sequences
        $counts = \array_count_values($values);
        foreach ($counts as $value => $count) {
            if ($count >= 3) {
                $score += Rules::SCORE_SEQUENCES[$value] * ($count - 2);
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

    /**
     * Check possible moves and end the game if there are no moves left
     *
     * @return bool True if the game ended
     */
    public static function endIfPossible(State $state): bool
    {
        if (self::hasPossibleMoves($state)) {
            return false;
        }

        $state->score = 0;
        $state->ended = true;

        return true;
    }

    public static function hasPossibleMoves(State $state): bool
    {
        return self::calcDicesScore($state->dices->toArray(), false) > 0;
    }

    /**
     * Take dices by colors
     *
     * @param array<non-empty-string> $colors
     * @param bool $remove Remove dices from the table
     * @return list<Dice>
     * @throws \Exception
     */
    public static function takeDices(State $state, array $colors, bool $remove = false): array
    {
        $chosen = [];
        foreach ($colors as $color) {
            foreach ($state->dices as $pos => $dice) {
                if ($dice->color === $color) {
                    $chosen[] = $dice;
                    if ($remove) {
                        $state->dices->removeByIndex($pos);
                    }

                    continue 2;
                }
            }

            throw new \Exception("Dice with color $color not found");
        }

        return $chosen;
    }
}
