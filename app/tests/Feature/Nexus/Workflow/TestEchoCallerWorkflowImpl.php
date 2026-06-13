<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Carbon\CarbonInterval;
use Temporal\Samples\Nexus\Service\EchoInput;
use Temporal\Samples\Nexus\Service\EchoOutput;
use Temporal\Samples\Nexus\Service\SampleNexusService;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationOptions;

class TestEchoCallerWorkflowImpl implements TestEchoCallerWorkflow
{
    public function echo(string $endpoint, string $message)
    {
        /** @var SampleNexusService $stub */
        $stub = Workflow::newNexusServiceStub(
            SampleNexusService::class,
            NexusOperationOptions::new()
                ->withEndpoint($endpoint)
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(20)),
        );
        /** @var EchoOutput $output */
        $output = yield $stub->echo(new EchoInput($message));
        return $output->message;
    }
}
