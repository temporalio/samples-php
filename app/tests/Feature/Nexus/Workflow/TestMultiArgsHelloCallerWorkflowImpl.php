<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Carbon\CarbonInterval;
use Temporal\Samples\NexusMultipleArguments\Service\HelloInput;
use Temporal\Samples\NexusMultipleArguments\Service\HelloOutput;
use Temporal\Samples\NexusMultipleArguments\Service\Language;
use Temporal\Samples\NexusMultipleArguments\Service\SampleNexusService;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationOptions;

class TestMultiArgsHelloCallerWorkflowImpl implements TestMultiArgsHelloCallerWorkflow
{
    public function hello(string $endpoint, string $name, Language $language)
    {
        /** @var SampleNexusService $service */
        $service = Workflow::newNexusServiceStub(
            SampleNexusService::class,
            NexusOperationOptions::new()
                ->withEndpoint($endpoint)
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(20)),
        );

        /** @var HelloOutput $output */
        $output = yield $service->hello(new HelloInput($name, $language));
        return $output->message;
    }
}
