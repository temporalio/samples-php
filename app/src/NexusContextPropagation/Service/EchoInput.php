<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Service;

final class EchoInput
{
    public function __construct(
        public readonly string $message,
    ) {}
}
