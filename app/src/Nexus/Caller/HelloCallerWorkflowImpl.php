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

class HelloCallerWorkflowImpl implements HelloCallerWorkflow
{
    private NexusOperationStubInterface $sampleNexusService;

    public function __construct()
    {
        $this->sampleNexusService = Workflow::newUntypedNexusOperationStub(
            NexusOperationOptions::new()
                ->withEndpoint(CallerWorker::ENDPOINT_NAME)
                ->withService('SampleNexusService')
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(10)),
        );
    }

    public function hello(string $message, Language $language)
    {
        /** @var NexusOperationHandle<HelloOutput> $handle */
        $handle = yield $this->sampleNexusService->start(
            'hello',
            [new HelloInput($message, $language)],
            HelloOutput::class,
        );

        Workflow::getLogger()->info(
            'Nexus operation started, token: ' . ($handle->getOperationToken() ?? '<sync>'),
        );

        /** @var HelloOutput $output */
        $output = yield $handle->getResult();

        return $output->message;
    }
}
