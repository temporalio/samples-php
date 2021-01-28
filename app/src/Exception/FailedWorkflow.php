<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Exception;

use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
class FailedWorkflow
{
    #[WorkflowMethod]
    public function getGreeting(
        string $name
    ) {
        $child = Workflow::newChildWorkflowStub(ChildWorkflow::class);

        return yield $child->composeGreeting('Hello', $name);
    }
}