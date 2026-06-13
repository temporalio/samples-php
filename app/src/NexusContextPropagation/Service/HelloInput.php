<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Service;

final class HelloInput
{
    public function __construct(
        public readonly string $name,
        public readonly Language $language,
    ) {}
}
