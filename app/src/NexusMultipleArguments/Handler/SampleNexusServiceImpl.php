<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Handler;

use Temporal\Client\WorkflowOptions;
use Temporal\Nexus\Nexus;
use Temporal\Nexus\WorkflowHandle;
use Temporal\Samples\NexusMultipleArguments\Service\EchoInput;
use Temporal\Samples\NexusMultipleArguments\Service\EchoOutput;
use Temporal\Samples\NexusMultipleArguments\Service\HelloInput;
use Temporal\Samples\NexusMultipleArguments\Service\SampleNexusService;

final class SampleNexusServiceImpl implements SampleNexusService
{
    private EchoClient $echoClient;

    public function __construct(?EchoClient $echoClient = null)
    {
        $this->echoClient = $echoClient ?? new EchoClientImpl();
    }

    public function echo(EchoInput $input): EchoOutput
    {
        return $this->echoClient->echo($input);
    }

    public function hello(HelloInput $input): WorkflowHandle
    {
        return WorkflowHandle::fromWorkflowMethod(
            HelloHandlerWorkflow::class,
            WorkflowOptions::new()->withWorkflowId(Nexus::getStartDetails()->requestId),
            $input->name,
            $input->language,
        );
    }
}
