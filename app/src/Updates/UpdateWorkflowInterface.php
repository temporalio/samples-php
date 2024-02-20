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
use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\UpdateMethod;
use Temporal\Workflow\UpdateValidatorMethod;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface UpdateWorkflowInterface
{
    #[WorkflowMethod('Zonk.start')]
    public function handle(int $maxTries = 5);

    #[UpdateMethod(name: 'rollDices')]
    public function roll();

    /**
     * @throws Exception
     */
    #[UpdateValidatorMethod(forUpdate: 'rollDices')]
    public function validateRoll(): void;

    #[SignalMethod(name: 'holdAndRoll')]
    public function holdAndRoll(array $colors);

    /**
     * Note: validation method must have the same signature as the update method.
     * @throws Exception
     */
    #[UpdateValidatorMethod(forUpdate: 'holdAndRoll')]
    public function validateHoldAndRoll(array $colors): void;

    #[SignalMethod]
    public function complete();
}