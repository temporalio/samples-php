<?php

declare(strict_types=1);

namespace Temporal\Samples\InfrequentPolling;

class ComposeGreetingActivity implements ComposeGreetingActivityInterface
{
    public function __construct(
        private TestService $service = new TestService()
    ) {
    }

    public function composeGreeting(string $greeting, string $name): string
    {
        return $this->service->getServiceResult($greeting, $name);
    }
}
