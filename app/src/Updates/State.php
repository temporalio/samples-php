<?php

declare(strict_types=1);

namespace Temporal\Samples\Updates;

use Temporal\Internal\Marshaller\Meta\MarshalArray;

final class State
{
    /**
     * Dices on the table
     *
     * @var list<Dice>
     */
    #[MarshalArray(of: Dice::class)]
    public array $dices = [];

    public int $tries = 3;
    public bool $ended = false;
    public int $score = 0;
}
