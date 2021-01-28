<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Child;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
class ChildWorkflow
{
    #[WorkflowMethod("Child.greet")]
    public function greet(
        string $name
    ) {
        return 'Hello ' . $name . ' from child workflow!';
    }
}