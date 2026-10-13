<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Handler;

use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;

class HelloHandlerWorkflowImpl implements HelloHandlerWorkflow
{
    public function hello(string $name, Language $language): HelloOutput
    {
        return match ($language) {
            Language::EN => new HelloOutput("Hello {$name} 👋"),
            Language::FR => new HelloOutput("Bonjour {$name} 👋"),
            Language::DE => new HelloOutput("Hallo {$name} 👋"),
            Language::ES => new HelloOutput("¡Hola! {$name} 👋"),
            Language::TR => new HelloOutput("Merhaba {$name} 👋"),
        };
    }
}
