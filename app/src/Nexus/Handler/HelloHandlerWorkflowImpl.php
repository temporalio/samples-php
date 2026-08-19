<?php

declare(strict_types=1);

namespace Temporal\Samples\Nexus\Handler;

use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;

class HelloHandlerWorkflowImpl implements HelloHandlerWorkflow
{
    public function hello(HelloInput $input): HelloOutput
    {
        switch ($input->language) {
            case Language::EN:
                return new HelloOutput("Hello {$input->name} 👋");
            case Language::FR:
                return new HelloOutput("Bonjour {$input->name} 👋");
            case Language::DE:
                return new HelloOutput("Hallo {$input->name} 👋");
            case Language::ES:
                return new HelloOutput("¡Hola! {$input->name} 👋");
            case Language::TR:
                return new HelloOutput("Merhaba {$input->name} 👋");
        }
        throw new ApplicationFailure(
            "Unsupported language: {$input->language->value}",
            'UNSUPPORTED_LANGUAGE',
            true,
        );
    }
}
