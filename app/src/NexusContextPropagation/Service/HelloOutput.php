<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Service;

final class HelloOutput
{
    public function __construct(
        public readonly string $message,
    ) {}
}
