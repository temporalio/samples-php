<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Carbon\CarbonInterval;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\Samples\Nexus\Service\SampleNexusService;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationOptions;

class TestHelloCallerWorkflowImpl implements TestHelloCallerWorkflow
{
    public function hello(string $endpoint, string $name, Language $language)
    {
        /** @var SampleNexusService $stub */
        $stub = Workflow::newNexusServiceStub(
            SampleNexusService::class,
            NexusOperationOptions::new()
                ->withEndpoint($endpoint)
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(20)),
        );
        /** @var HelloOutput $output */
        $output = yield $stub->hello(new HelloInput($name, $language));
        return $output->message;
    }
}
