<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Signal;

use Temporal\Workflow;

/**
 * Demonstrates asynchronous signalling of a workflow. Requires a local instance of Temporal server
 * to be running.
 */
class SignalWorkflow implements SignalWorkflowInterface
{
    private array $input = [];
    private bool $exit = false;

    public function greet()
    {
        $result = [];
        while (true) {
            yield Workflow::await(fn() => $this->input !== [] || $this->exit);
            if ($this->input === [] && $this->exit) {
                return $result;
            }

            $name = array_shift($this->input);
            $result[] = sprintf('Hello, %s!', $name);
        }
    }

    public function addName(string $name): void
    {
        $this->input[] = $name;
    }

    public function exit(): void
    {
        $this->exit = true;
    }
}