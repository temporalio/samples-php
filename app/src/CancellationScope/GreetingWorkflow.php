<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\CancellationScope;

use Carbon\CarbonInterval;
use React\Promise\Deferred;
use Temporal\Activity\ActivityCancellationType;
use Temporal\Activity\ActivityOptions;
use Temporal\Exception\Failure\ActivityFailure;
use Temporal\Exception\Failure\CanceledFailure;
use Temporal\Promise;
use Temporal\Workflow;

/**
 * The sample executes multiple activities in parallel. Then it waits for one of them to finish,
 * cancels all others and waits for their cancellation completion.
 *
 * <p>The cancellation is done through {@link Workflow::async()->cancel()}.
 *
 * <p>Note that ActivityOptions->cancellationType is set to WAIT_CANCELLATION_COMPLETED. Otherwise
 * the activity completion promise is not going to wait for the activity to finish cancellation.
 *
 * Experiment with number of activity workers to observe different cancellation behaviour.
 */
class GreetingWorkflow implements GreetingWorkflowInterface
{
    /** @var GreetingActivityInterface */
    private $greetingActivity;

    private array $messages = ["Hello", "Bye", "Hola", "Привет", "Oi", "Hallo"];

    public function __construct()
    {
        $this->greetingActivity = Workflow::newActivityStub(
            GreetingActivityInterface::class,
            ActivityOptions::new()
                ->withHeartbeatTimeout(6)
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(100))
                ->withCancellationType(ActivityCancellationType::WAIT_CANCELLATION_COMPLETED)
        );
    }

    public function greet(string $name)
    {
        $results = [];

        $scope = Workflow::async(
            function () use ($name, &$results) {
                foreach ($this->messages as $i => $msg) {
                    $results[] = $this->greetingActivity->composeGreeting($msg, $name);
                }
            }
        );

        // Wait for at least one activity to complete
        yield Promise::any($results);

        // Cancel all remaining activities
        $scope->cancel();

        $values = [];

        // Wait for all activities to complete ignoring cancellations
        foreach ($results as $promise) {
            try {
                $values[] = yield $promise;
            } catch (ActivityFailure $e) {
                if (!$e->getPrevious() instanceof CanceledFailure) {
                    throw $e;
                }
            }
        }

        return $values;
    }
}