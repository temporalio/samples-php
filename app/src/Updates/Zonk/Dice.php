<?php

declare(strict_types=1);

namespace Temporal\Samples\Updates\Zonk;

use Temporal\Internal\Marshaller\Meta\Marshal;

final class Dice
{
    private const COLORS = ['red', 'green', 'blue', 'yellow', 'cyan', 'magenta'];

    #[Marshal]
    public readonly string $color;
    #[Marshal]
    private int $value = 0;

    public function __construct(int $num)
    {
        $this->color = self::COLORS[$num % \count(self::COLORS)];
    }

    /**
     * @return int<1, 6>
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function reRoll(): self
    {
        $clone = clone $this;
        $clone->roll();
        return $clone;
    }

    private function roll(): int
    {
        return $this->value = \random_int(1, 6);
    }
}
