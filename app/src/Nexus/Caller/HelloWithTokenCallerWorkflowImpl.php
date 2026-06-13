<?php

declare(strict_types=1);

namespace Temporal\Samples\Nexus\Caller;

use Carbon\CarbonInterval;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\Workflow;
use Temporal\Workflow\NexusOperationHandle;
use Temporal\Workflow\NexusOperationOptions;
use Temporal\Workflow\NexusOperationStubInterface;

class HelloWithTokenCallerWorkflowImpl implements HelloWithTokenCallerWorkflow
{
    private NexusOperationStubInterface $stub;

    public function __construct()
    {
        $this->stub = Workflow::newUntypedNexusOperationStub(
            NexusOperationOptions::new()
                ->withEndpoint(CallerWorker::ENDPOINT_NAME)
                ->withService('SampleNexusService')
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(10)),
        );
    }

    public function hello(string $message, Language $language)
    {
        /** @var NexusOperationHandle<HelloOutput> $handle */
        $handle = yield $this->stub->start(
            'hello',
            [new HelloInput($message, $language)],
            HelloOutput::class,
        );

        $token = $handle->getOperationToken() ?? '<sync>';
        Workflow::getLogger()->info("Nexus operation started, token: {$token}");

        /** @var HelloOutput $output */
        $output = yield $handle->getResult();

        return "[token={$token}] {$output->message}";
    }
}
