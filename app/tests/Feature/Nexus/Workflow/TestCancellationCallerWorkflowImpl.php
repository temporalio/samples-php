<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Carbon\CarbonInterval;
use Temporal\Exception\Failure\CanceledFailure;
use Temporal\Exception\Failure\NexusOperationFailure;
use Temporal\Promise;
use Temporal\Samples\NexusCancellation\Service\HelloInput;
use Temporal\Samples\NexusCancellation\Service\HelloOutput;
use Temporal\Samples\NexusCancellation\Service\Language;
use Temporal\Samples\NexusCancellation\Service\SampleNexusService;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationCancellationType;
use Temporal\Workflow\NexusOperationOptions;

/**
 * Same fan-out + cancel-the-rest flow as the NexusCancellation sample caller,
 * with the endpoint passed in so each test run can use its own endpoint.
 */
class TestCancellationCallerWorkflowImpl implements TestCancellationCallerWorkflow
{
    public function hello(string $endpoint, string $message)
    {
        /** @var SampleNexusService $service */
        $service = Workflow::newNexusServiceStub(
            SampleNexusService::class,
            NexusOperationOptions::new()
                ->withEndpoint($endpoint)
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(20))
                ->withCancellationType(NexusOperationCancellationType::WaitRequested),
        );

        $promises = [];
        $scope = Workflow::async(function () use ($service, $message, &$promises): void {
            foreach (Language::cases() as $language) {
                $promises[] = $service->hello(new HelloInput($message, $language));
            }
        });

        /** @var HelloOutput $first */
        $first = yield Promise::any($promises);

        $scope->cancel();

        foreach ($promises as $promise) {
            try {
                yield $promise;
            } catch (CanceledFailure) {
            } catch (NexusOperationFailure $e) {
                if (!$e->getPrevious() instanceof CanceledFailure) {
                    throw $e;
                }
            }
        }

        return $first->message;
    }
}
