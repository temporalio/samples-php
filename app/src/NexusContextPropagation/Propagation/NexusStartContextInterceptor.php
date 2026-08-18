<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Propagation;

use Temporal\Workflow\WorkflowExecution;
use Temporal\Interceptor\Header;
use Temporal\Interceptor\Trait\WorkflowClientCallsInterceptorTrait;
use Temporal\Interceptor\WorkflowClient\StartInput;
use Temporal\Interceptor\WorkflowClientCallsInterceptor;
use Temporal\Nexus\Nexus;

final class NexusStartContextInterceptor implements WorkflowClientCallsInterceptor
{
    use WorkflowClientCallsInterceptorTrait;

    private const NEXUS_HEADER_PREFIX = 'x-nexus-';

    public function start(StartInput $input, callable $next): WorkflowExecution
    {
        $propagated = self::propagatedHeaders();
        if ($propagated === []) {
            return $next($input);
        }

        return $next($input->with(header: Header::fromValues($propagated)));
    }

    /**
     * @return array<string, string>
     */
    private static function propagatedHeaders(): array
    {
        try {
            $headers = Nexus::getCurrentOperationContext()->headers->all();
        } catch (\LogicException) {
            return [];
        }

        $propagated = [];
        foreach ($headers as $name => $value) {
            if (\str_starts_with($name, self::NEXUS_HEADER_PREFIX)) {
                $propagated[$name] = $value;
            }
        }

        return $propagated;
    }
}
