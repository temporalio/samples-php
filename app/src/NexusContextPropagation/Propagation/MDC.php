<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Propagation;

final class MDC
{
    /** @var array<string, string> */
    private static array $context = [];

    public static function put(string $key, string $value): void
    {
        self::$context[$key] = $value;
    }

    public static function get(string $key): ?string
    {
        return self::$context[$key] ?? null;
    }

    /** @return array<string, string> */
    public static function getAll(): array
    {
        return self::$context;
    }

    public static function clear(): void
    {
        self::$context = [];
    }
}
