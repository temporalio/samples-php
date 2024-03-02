<?php

declare(strict_types=1);

namespace Temporal\Samples\Updates\Zonk;

final class State
{
    public bool $ended = false;
    public int $score = 0;
    public bool $canRoll = true;

    public function __construct(
        public Table $dices = new Table(),
    ) {
    }
}
