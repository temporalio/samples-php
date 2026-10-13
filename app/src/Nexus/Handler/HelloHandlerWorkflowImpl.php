<?php

declare(strict_types=1);

namespace Temporal\Samples\Nexus\Handler;

use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;

class HelloHandlerWorkflowImpl implements HelloHandlerWorkflow
{
    public function hello(HelloInput $input): HelloOutput
    {
        return match ($input->language) {
            Language::EN => new HelloOutput("Hello {$input->name} 👋"),
            Language::FR => new HelloOutput("Bonjour {$input->name} 👋"),
            Language::DE => new HelloOutput("Hallo {$input->name} 👋"),
            Language::ES => new HelloOutput("¡Hola! {$input->name} 👋"),
            Language::TR => new HelloOutput("Merhaba {$input->name} 👋"),
        };
    }
}
