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
        Rules::endIfPossible($this->state);
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
        $dices = Rules::takeDices($this->state, $colors, true);
        // Calculate score
        $score = Rules::calcDicesScore($dices);
        $this->state->score += $score;

        if ($this->state->dices === []) {
            // Reset dices if there are no dices left
            yield $this->resetDices();
        } else {
            // Roll the remaining dices
            yield $this->rollDices();
        }

        // Check if the game ended
        Rules::endIfPossible($this->state);

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
        $dices = Rules::takeDices($this->state, $colors);
        // Scores are calculated here
        Rules::calcDicesScore($dices);
    }

    public function complete()
    {
        Rules::hasPossibleMoves($this->state) or --$this->state->tries;

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
     * Remove all dices from the table and create new ones
     */
    private function resetDices(): PromiseInterface
    {
        $this->state->dices = [];
        for ($i = 0; $i < Rules::DICES_COUNT; $i++) {
            $this->state->dices[] = new Dice($i);
        }

        return $this->rollDices();
    }
}