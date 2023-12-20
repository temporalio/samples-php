<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Schedule;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface ScheduleWorkflowInterface
{
    public const WORKFLOW_TYPE = 'Schedule.greet';
    public const WORKFLOW_ID = 'ScheduledWorkflowID';
    public const SCHEDULE_ID = 'ScheduleID';

    #[WorkflowMethod(name: self::WORKFLOW_TYPE)]
    public function greet(string $name);
}