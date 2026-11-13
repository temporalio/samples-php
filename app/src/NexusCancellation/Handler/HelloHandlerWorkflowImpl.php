<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusCancellation\Handler;

use Carbon\CarbonInterval;
use Temporal\Exception\Failure\CanceledFailure;
use Temporal\Samples\Nexus\Handler\HelloHandlerWorkflow;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\HelloOutput;
use Temporal\Samples\Nexus\Service\Language;
use Temporal\Workflow;

class HelloHandlerWorkflowImpl implements HelloHandlerWorkflow
{
    public function hello(HelloInput $input): \Generator
    {
        try {
            yield Workflow::timer(CarbonInterval::seconds(yield Workflow::sideEffect(fn() => \random_int(0, 4))));

            return match ($input->language) {
                Language::EN => new HelloOutput("Hello {$input->name} 👋"),
                Language::FR => new HelloOutput("Bonjour {$input->name} 👋"),
                Language::DE => new HelloOutput("Hallo {$input->name} 👋"),
                Language::ES => new HelloOutput("¡Hola! {$input->name} 👋"),
                Language::TR => new HelloOutput("Merhaba {$input->name} 👋"),
            };
        } catch (CanceledFailure $e) {
            yield Workflow::asyncDetached(function () {
                yield Workflow::timer(CarbonInterval::seconds(yield Workflow::sideEffect(fn() => \random_int(0, 4))));
            });

            Workflow::getLogger()->info('HelloHandlerWorkflow was cancelled successfully.');

            throw $e;
        }
    }
}
