<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusCancellation\Caller;

use Carbon\CarbonInterval;
use Temporal\Exception\Failure\CanceledFailure;
use Temporal\Exception\Failure\NexusOperationFailure;
use Temporal\Promise;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\Samples\Nexus\Service\SampleNexusService;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationCancellationType;
use Temporal\Workflow\NexusOperationOptions;

class HelloCallerWorkflowImpl implements HelloCallerWorkflow
{
    /** @var SampleNexusService */
    private object $sampleNexusService;

    public function __construct()
    {
        $this->sampleNexusService = Workflow::newNexusServiceStub(
            SampleNexusService::class,
            NexusOperationOptions::new()
                ->withEndpoint(CallerWorker::ENDPOINT_NAME)
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(10))
                ->withCancellationType(NexusOperationCancellationType::WaitRequested),
        );
    }

    public function hello(string $message)
    {
        $promises = [];

        $scope = Workflow::async(function () use ($message, &$promises): void {
            foreach (Language::cases() as $language) {
                $promises[] = $this->sampleNexusService->hello(new HelloInput($message, $language));
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
