<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Saga;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface SagaWorkflowInterface
{
    /**
     * Main saga workflow. Here we execute activity operation twice (first from a child workflow,
     * second directly using activity stub), add three compensation functions, and then throws some
     * exception in workflow code. When we catch the exception, saga.compensate will run the
     * compensation functions according to the policy specified in SagaOptions.
     */
    #[WorkflowMethod("Saga")]
    public function execute();
}