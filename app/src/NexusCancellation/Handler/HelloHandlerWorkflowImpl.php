<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusCancellation\Handler;

use Carbon\CarbonInterval;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Exception\Failure\CanceledFailure;
use Temporal\Samples\NexusCancellation\Service\HelloInput;
use Temporal\Samples\NexusCancellation\Service\HelloOutput;
use Temporal\Samples\NexusCancellation\Service\Language;
use Temporal\Workflow;

class HelloHandlerWorkflowImpl implements HelloHandlerWorkflow
{
    public function hello(HelloInput $input)
    {
        try {
            yield Workflow::timer(CarbonInterval::seconds(yield Workflow::sideEffect(fn() => \random_int(0, 4))));

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
                false,
            );
        } catch (CanceledFailure $e) {
            yield Workflow::asyncDetached(function () {
                yield Workflow::timer(CarbonInterval::seconds(yield Workflow::sideEffect(fn() => \random_int(0, 4))));
            });

            Workflow::getLogger()->info('HelloHandlerWorkflow was cancelled successfully.');

            throw $e;
        }
    }
}
