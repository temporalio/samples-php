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
    private State $state;

    public function handle(int $maxTries = 3)
    {
        $this->state = new State();

        yield Workflow::await(fn() => $this->state->ended);

        return $this->state;
    }

    public function roll()
    {
        yield $this->state->dices === []
            ? $this->resetDices()
            : $this->rollDices();

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
        $this->state->canRoll or throw new \Exception('Invalid roll action');
    }

    public function choose(array $colors)
    {
        // Take dices
        $dices = Rules::takeDices($this->state, $colors, true);
        // Calculate score
        $this->state->score += Rules::calcDicesScore($dices);
        // Unlock Roll action
        $this->state->canRoll = true;

        return $this->state;
    }

    /**
     * The method validates the move is possible in the current game state.
     *
     * Note: validation method must have the same signature as the update method.
     * @throws Exception
     */
    public function validateChoose(array $colors): void
    {
        $this->state->ended and throw new \Exception('Game ended');
        $colors === [] and throw new \Exception('You must pick at least one dice');
        \count($colors) <= \count($this->state->dices) or throw new \Exception('Invalid dices count');
        \array_unique($colors) === $colors or throw new \Exception('You can not use the same dice twice');
        // Chosen dices are available
        $dices = Rules::takeDices($this->state, $colors);
        // Scores are calculated here
        Rules::calcDicesScore($dices);
    }

    public function complete()
    {
        $this->state->ended = true;
        return $this->state;
    }

    public function getState(): State
    {
        return $this->state;
    }

    private function rollDices(): PromiseInterface
    {
        $dices = $this->state->dices;
        $promises = $this->state->dices = [];
        $this->state->canRoll = false;
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