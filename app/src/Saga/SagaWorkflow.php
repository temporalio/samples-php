<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Saga;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\SampleUtils\Logger;
use Temporal\Workflow;

/**
 * Demonstrates implementing saga transaction and compensation logic using Temporal.
 *
 * @see TripBookingWorkflow for another SAGA example.
 */
class SagaWorkflow implements SagaWorkflowInterface
{
    private $activity;

    public function __construct()
    {
        $this->activity = Workflow::newActivityStub(
            SampleActivityInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(2))
        );
    }

    public function execute()
    {
        $saga = new Workflow\Saga();
        $saga->setParallelCompensation(true);

        try {
            // The following demonstrate how to compensate sync invocations.
            $child = Workflow::newChildWorkflowStub(ChildWorkflowInterface::class);
            yield $child->execute(10);

            $saga->addCompensation(
                function () {
                    $childCompensation = Workflow::newChildWorkflowStub(CompensateChildWorkflowOperation::class);
                    yield $childCompensation->compensate(10);
                }
            );

            // The following demonstrate how to compensate async invocations.
            $execute = $this->activity->execute(20);
            $saga->addCompensation(fn() => yield $this->activity->compensate(20));

            yield $execute;

            // The following demonstrate the ability of supplying arbitrary lambda as a saga
            // compensation function. In production code please always use Workflow.getLogger
            // to log messages in workflow code.
            $saga->addCompensation(
                function () {
                    (new Logger())->debug("running custom compensation");
                }
            );

            throw new \RuntimeException("some error");
        } catch (\Throwable $e) {
            yield $saga->compensate();
            return "saga was compensated";
        }
    }
}