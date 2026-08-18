<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Caller;

use Carbon\CarbonInterval;
use Temporal\Samples\NexusContextPropagation\Propagation\MDC;
use Temporal\Samples\Nexus\Caller\EchoCallerWorkflow;
use Temporal\Samples\Nexus\Service\EchoInput;
use Temporal\Samples\Nexus\Service\EchoOutput;
use Temporal\Samples\Nexus\Service\SampleNexusService;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationOptions;

class EchoCallerWorkflowImpl implements EchoCallerWorkflow
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

    public function echo(string $message)
    {
        MDC::put('x-nexus-caller-workflow-id', Workflow::getInfo()->execution->getID());

        /** @var EchoOutput $output */
        $output = yield $this->sampleNexusService->echo(new EchoInput($message));
        return $output->message;
    }
}
