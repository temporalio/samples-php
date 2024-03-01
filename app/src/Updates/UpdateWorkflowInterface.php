<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Updates;

use DateTimeInterface;
use Exception;
use Temporal\Workflow\QueryMethod;
use Temporal\Workflow\ReturnType;
use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\UpdateMethod;
use Temporal\Workflow\UpdateValidatorMethod;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface UpdateWorkflowInterface
{
    #[WorkflowMethod('Zonk.start')]
    #[ReturnType(State::class)]
    public function handle(int $maxTries = 5);

    /**
     * @return State
     */
    #[UpdateMethod(name: 'rollDices')]
    #[ReturnType(State::class)]
    public function roll();

    #[UpdateValidatorMethod(forUpdate: 'rollDices')]
    public function validateRoll(): void;

    /**
     * @param list<non-empty-string> $colors
     * @return State
     */
    #[UpdateMethod(name: 'holdAndRoll')]
    #[ReturnType(State::class)]
    public function holdAndRoll(array $colors);

    #[UpdateValidatorMethod(forUpdate: 'holdAndRoll')]
    public function validateHoldAndRoll(array $colors): void;

    /**
     * Stop the game and save the score
     *
     * Note: the method has no validator
     */
    #[UpdateMethod]
    #[ReturnType(State::class)]
    public function complete();

    #[QueryMethod]
    public function getState(): State;

    #[SignalMethod]
    public function exit();
}
