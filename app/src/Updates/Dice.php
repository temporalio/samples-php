<?php

declare(strict_types=1);

namespace Temporal\Samples\Updates;

final class Dice
{
    private const COLORS = ['red', 'green', 'blue', 'yellow', 'black', 'cyan', 'magenta', 'white'];

    public readonly string $color;
    private int $value;

    public function __construct(int $num)
    {
        $this->color = self::COLORS[$num % \count(self::COLORS)];
        $this->roll();
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function reRoll(): int
    {
        return $this->roll();
    }

    private function roll(): int
    {
        return $this->value = \random_int(1, 6);
    }
}
