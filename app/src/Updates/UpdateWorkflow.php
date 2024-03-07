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
use Temporal\Samples\Updates\Zonk\Rules;
use Temporal\Samples\Updates\Zonk\State;
use Temporal\Samples\Updates\Zonk\Table;
use Temporal\Workflow;

/**
 * Demonstrates the Workflow Update feature using a turn of the game Zonk (Farkle) as an example.
 * The state of the virtual table with dice is stored in the Workflow.
 * All player actions are performed through Update functions with pre-validation.
 * Invalid actions will be rejected at the validation stage.
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
        $this->state->dices = yield $this->state->dices->isEmpty()
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

    /**
     * @return PromiseInterface<Table>
     */
    private function rollDices(): PromiseInterface
    {
        $this->state->canRoll = false;
        // We have to use SideEffect here to make random actions in deterministic way
        // Might be replaced with an activity call
        return Workflow::sideEffect(fn(): Table => $this->state->dices->rollDices());
    }

    /**
     * Remove all dices from the table and create new ones
     * @return PromiseInterface<Table>
     */
    private function resetDices(): PromiseInterface
    {
        $this->state->canRoll = false;
        // We have to use SideEffect here to make random actions in deterministic way
        // Might be replaced with an activity call
        return Workflow::sideEffect(fn(): Table => $this->state->dices->resetDices());
    }
}