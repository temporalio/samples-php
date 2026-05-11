<?php

declare(strict_types=1);

namespace Temporal\Samples\InfrequentPolling;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: 'InfrequentPolling.')]
interface ComposeGreetingActivityInterface
{
    #[ActivityMethod(name: 'ComposeGreeting')]
    public function composeGreeting(
        string $greeting,
        string $name
    ): string;
}
