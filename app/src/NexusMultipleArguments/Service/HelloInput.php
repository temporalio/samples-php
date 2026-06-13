<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Service;

final class HelloInput
{
    public function __construct(
        public readonly string $name,
        public readonly Language $language,
    ) {}
}
