<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Handler;

use Temporal\Client\WorkflowOptions;
use Temporal\Nexus\Nexus;
use Temporal\Nexus\WorkflowHandle;
use Temporal\Samples\Nexus\Handler\EchoClient;
use Temporal\Samples\Nexus\Handler\EchoClientImpl;
use Temporal\Samples\Nexus\Service\EchoInput;
use Temporal\Samples\Nexus\Service\EchoOutput;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\SampleNexusService;

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
