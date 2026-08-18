<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Handler;

use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\NexusContextPropagation\Propagation\MDC;
use Temporal\Samples\Nexus\Service\Language;

class HelloHandlerWorkflowImpl implements HelloHandlerWorkflow
{
    public function hello(HelloInput $input): HelloOutput
    {
        $callerWorkflowId = MDC::get('x-nexus-caller-workflow-id');
        $name = $callerWorkflowId === null
            ? $input->name
            : $input->name . ', x-nexus-caller-workflow-id: ' . $callerWorkflowId;

        switch ($input->language) {
            case Language::EN:
                return new HelloOutput("Hello {$name} 👋");
            case Language::FR:
                return new HelloOutput("Bonjour {$name} 👋");
            case Language::DE:
                return new HelloOutput("Hallo {$name} 👋");
            case Language::ES:
                return new HelloOutput("¡Hola! {$name} 👋");
            case Language::TR:
                return new HelloOutput("Merhaba {$name} 👋");
        }
        throw new ApplicationFailure(
            "Unsupported language: {$input->language->value}",
            'UNSUPPORTED_LANGUAGE',
            true,
        );
    }
}
