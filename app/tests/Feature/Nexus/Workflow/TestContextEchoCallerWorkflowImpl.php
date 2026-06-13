<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Carbon\CarbonInterval;
use Temporal\Samples\NexusContextPropagation\Propagation\MDC;
use Temporal\Samples\NexusContextPropagation\Service\EchoInput;
use Temporal\Samples\NexusContextPropagation\Service\EchoOutput;
use Temporal\Samples\NexusContextPropagation\Service\SampleNexusService;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationOptions;

class TestContextEchoCallerWorkflowImpl implements TestContextEchoCallerWorkflow
{
    public function echo(string $endpoint, string $message)
    {
        MDC::put('x-nexus-caller-workflow-id', Workflow::getInfo()->execution->getID());

        /** @var SampleNexusService $service */
        $service = Workflow::newNexusServiceStub(
            SampleNexusService::class,
            NexusOperationOptions::new()
                ->withEndpoint($endpoint)
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(20)),
        );

        /** @var EchoOutput $output */
        $output = yield $service->echo(new EchoInput($message));
        return $output->message;
    }
}
