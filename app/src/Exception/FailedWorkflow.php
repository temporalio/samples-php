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

/**
 * Demonstrates exception propagation across activity, child workflow and workflow client
 * boundaries. Shows how to deal with checked exceptions.
 *
 * <li>
 *     <ul>
 *        Exceptions thrown by an activity are received by the workflow wrapped into an {@linkio ActivityFailure}.
 * </ul>
 *
 * <ul>
 *   Exceptions thrown by a child workflow are received by a parent workflow wrapped into a {@link ChildWorkflowFailure}.
 * </ul>
 *
 * <ul>
 *   Exceptions thrown by a workflow are received by a workflow client wrapped into {@link WorkflowFailedException}.
 * </ul>
 *
 * <p>In this example a Workflow Client executes a workflow which executes a child workflow which
 * executes an activity which throws an Error.
 *
 * Note that there is only one level of wrapping when crossing logical process boundaries. And that
 * wrapper exception adds a lot of relevant information about failure.
 *
 * P.S. This workflow also implemented without parent interface.
 */
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