<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Carbon\CarbonInterval;
use Temporal\Samples\NexusContextPropagation\Propagation\MDC;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\Samples\Nexus\Service\SampleNexusService;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationOptions;

class TestContextHelloCallerWorkflowImpl implements TestContextHelloCallerWorkflow
{
    public function hello(string $endpoint, string $name)
    {
        MDC::put('x-nexus-caller-workflow-id', Workflow::getInfo()->execution->getID());

        /** @var SampleNexusService $service */
        $service = Workflow::newNexusServiceStub(
            SampleNexusService::class,
            NexusOperationOptions::new()
                ->withEndpoint($endpoint)
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(20)),
        );

        /** @var HelloOutput $output */
        $output = yield $service->hello(new HelloInput($name, Language::EN));
        return $output->message;
    }
}
