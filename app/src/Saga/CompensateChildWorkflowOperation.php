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
interface CompensateChildWorkflowOperation
{
    /** @return void */
    #[WorkflowMethod("Saga.Compensate")]
    public function compensate(
        int $amount
    );
}