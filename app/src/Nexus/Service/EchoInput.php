<?php

declare(strict_types=1);

namespace Temporal\Samples\Nexus\Service;

final class EchoInput
{
    public function __construct(
        public readonly string $message,
    ) {}
}
