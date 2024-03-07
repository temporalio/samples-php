<?php

declare(strict_types=1);

namespace Temporal\Samples\Updates\Zonk;

use Countable;
use IteratorAggregate;
use Temporal\Internal\Marshaller\Meta\MarshalArray;
use Traversable;

final class Table implements IteratorAggregate, Countable
{
    /**
     * Dices on the table
     *
     * @var array<int, Dice>
     */
    #[MarshalArray(of: Dice::class)]
    public array $dices = [];

    /**
     * @return Traversable<Dice>
     */
    public function getIterator(): Traversable
    {
        yield from $this->dices;
    }

    /**
     * @return int<0, 6>
     */
    public function count(): int
    {
        return \count($this->dices);
    }

    public function isEmpty(): bool
    {
        return $this->dices === [];
    }

    public function hasIndex(int $index): bool
    {
        return isset($this->dices[$index]);
    }

    public function getByIndex(int $index): Dice
    {
        return $this->dices[$index] ?? throw new \InvalidArgumentException(
            \sprintf('Dice with index %d not found', $index)
        );
    }

    /**
     * Remove dices from the table
     */
    public function clear(): void
    {
        $this->dices = [];
    }

    /**
     * Add dice to the table
     */
    public function add(Dice $dice): void
    {
        $this->dices[] = $dice;
    }

    /**
     * @return array<int, int<1, 6>>
     */
    public function getValues(): array
    {
        return \array_map(static fn(Dice $dice): int => $dice->getValue(), $this->dices);
    }

    public function removeByIndex(int $pos): void
    {
        unset($this->dices[$pos]);
    }

    /**
     * @return array<int, Dice>
     */
    public function toArray(): array
    {
        return $this->dices;
    }

    /**
     * Reroll all existing dices and return new table
     * The method is not deterministic
     */
    public function rollDices(): self
    {
        $result = new self();

        foreach ($this->dices as $dice) {
            $result->add($dice->reRoll());
        }

        return $result;
    }

    /**
     * Remove all dices from the table and create new ones
     * The method is not deterministic
     * @return self New table
     */
    public function resetDices(): self
    {
        $result = new self();

        for ($i = 0; $i < Rules::DICES_COUNT; $i++) {
            $result->add((new Dice($i))->reRoll());
        }

        return $result;
    }
}
