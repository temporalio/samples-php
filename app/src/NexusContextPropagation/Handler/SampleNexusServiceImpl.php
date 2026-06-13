<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Handler;

use Temporal\Client\WorkflowOptions;
use Temporal\Nexus\Nexus;
use Temporal\Nexus\WorkflowHandle;
use Temporal\Samples\NexusContextPropagation\Service\EchoInput;
use Temporal\Samples\NexusContextPropagation\Service\EchoOutput;
use Temporal\Samples\NexusContextPropagation\Service\HelloInput;
use Temporal\Samples\NexusContextPropagation\Service\SampleNexusService;

final class SampleNexusServiceImpl implements SampleNexusService
{
    private EchoClient $echoClient;

    public function __construct(?EchoClient $echoClient = null)
    {
        $this->echoClient = $echoClient ?? new EchoClientImpl();
    }

    public function echo(EchoInput $input): EchoOutput
    {
        $headers = Nexus::getCurrentOperationContext()->headers;
        if (($id = $headers->get('x-nexus-caller-workflow-id')) !== null) {
            \error_log("Echo called from a workflow with ID : {$id}");
        }

        return $this->echoClient->echo($input);
    }

    public function hello(HelloInput $input): WorkflowHandle
    {
        $headers = Nexus::getCurrentOperationContext()->headers;
        if (($id = $headers->get('x-nexus-caller-workflow-id')) !== null) {
            \error_log("HelloHandlerWorkflow called from a workflow with ID : {$id}");
        }

        return WorkflowHandle::fromWorkflowMethod(
            HelloHandlerWorkflow::class,
            WorkflowOptions::new()->withWorkflowId(Nexus::getStartDetails()->requestId),
            $input,
        );
    }
}
