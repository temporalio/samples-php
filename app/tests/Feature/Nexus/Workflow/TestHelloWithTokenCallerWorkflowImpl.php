<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Workflow;

use Carbon\CarbonInterval;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\Samples\Nexus\Service\SampleNexusService;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationHandle;
use Temporal\Workflow\NexusOperationOptions;

class TestHelloWithTokenCallerWorkflowImpl implements TestHelloWithTokenCallerWorkflow
{
    public function hello(string $endpoint, string $name, Language $language)
    {
        $stub = Workflow::newUntypedNexusOperationStub(
            NexusOperationOptions::new()
                ->withEndpoint($endpoint)
                ->withService('SampleNexusService')
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(20)),
        );

        /** @var NexusOperationHandle<HelloOutput> $handle */
        $handle = yield $stub->start(
            'hello',
            [new HelloInput($name, $language)],
            HelloOutput::class,
        );

        $token = $handle->getOperationToken();

        /** @var HelloOutput $output */
        $output = yield $handle->getResult();

        return ['token' => $token, 'message' => $output->message];
    }
}
