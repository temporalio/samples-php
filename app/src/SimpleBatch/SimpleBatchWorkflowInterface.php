<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SimpleBatch;

use Temporal\Workflow\QueryMethod;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface SimpleBatchWorkflowInterface
{
    public const WORKFLOW_ID = 'simple-batch-workflow';

    #[WorkflowMethod(name: "SimpleBatch")]
    public function start(int $batchId, array $options);

    #[QueryMethod]
    public function getAvailableResults(): array;

    #[QueryMethod]
    public function getPendingTasks(): array;
}
