<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Caller;

use Carbon\CarbonInterval;
use Temporal\Samples\NexusContextPropagation\Propagation\MDC;
use Temporal\Samples\Nexus\Caller\HelloCallerWorkflow;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\Samples\Nexus\Service\SampleNexusService;
use Temporal\Workflow;
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
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(10)),
        );
    }

    public function hello(string $message, Language $language)
    {
        MDC::put('x-nexus-caller-workflow-id', Workflow::getInfo()->execution->getID());

        /** @var HelloOutput $output */
        $output = yield $this->sampleNexusService->hello(new HelloInput($message, $language));
        return $output->message;
    }
}
