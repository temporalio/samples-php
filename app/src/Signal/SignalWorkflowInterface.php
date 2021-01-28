<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Signal;

use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface SignalWorkflowInterface
{
    /**
     * @return []string
     */
    #[WorkflowMethod(name: "Signal.greet")]
    public function greet();

    /**
     * Receives name through an external signal.
     * @param string $name
     */
    #[SignalMethod]
    public function addName(
        string $name
    ): void;

    #[SignalMethod]
    public function exit(): void;
}