<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Handler;

use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Samples\NexusMultipleArguments\Service\HelloOutput;
use Temporal\Samples\NexusMultipleArguments\Service\Language;

class HelloHandlerWorkflowImpl implements HelloHandlerWorkflow
{
    public function hello(string $name, Language $language): HelloOutput
    {
        switch ($language) {
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
            "Unsupported language: {$language->value}",
            'UNSUPPORTED_LANGUAGE',
            false,
        );
    }
}
