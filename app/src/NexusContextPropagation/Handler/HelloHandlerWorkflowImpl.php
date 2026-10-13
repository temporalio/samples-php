<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Handler;

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

        return match ($input->language) {
            Language::EN => new HelloOutput("Hello {$name} 👋"),
            Language::FR => new HelloOutput("Bonjour {$name} 👋"),
            Language::DE => new HelloOutput("Hallo {$name} 👋"),
            Language::ES => new HelloOutput("¡Hola! {$name} 👋"),
            Language::TR => new HelloOutput("Merhaba {$name} 👋"),
        };
    }
}
