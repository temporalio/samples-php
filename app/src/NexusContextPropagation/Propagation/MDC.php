<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Propagation;

use Temporal\Workflow;

final class MDC
{
    /** @var array<string, array<string, string>> */
    private static array $contexts = [];

    public static function put(string $key, string $value): void
    {
        self::$contexts[self::scope()][$key] = $value;
    }

    public static function get(string $key): ?string
    {
        return self::$contexts[self::scope()][$key] ?? null;
    }

    /** @return array<string, string> */
    public static function getAll(): array
    {
        return self::$contexts[self::scope()] ?? [];
    }

    public static function clear(): void
    {
        unset(self::$contexts[self::scope()]);
    }

    private static function scope(): string
    {
        return Workflow::getInfo()->execution->getID();
    }
}
