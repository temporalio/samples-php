<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Propagation;

use React\Promise\PromiseInterface;
use Temporal\Interceptor\Trait\WorkflowOutboundCallsInterceptorTrait;
use Temporal\Interceptor\WorkflowOutboundCalls\ExecuteNexusOperationInput;
use Temporal\Interceptor\WorkflowOutboundCallsInterceptor;

final class NexusOutboundContextInterceptor implements WorkflowOutboundCallsInterceptor
{
    use WorkflowOutboundCallsInterceptorTrait;

    private const NEXUS_HEADER_PREFIX = 'x-nexus-';

    public function executeNexusOperation(ExecuteNexusOperationInput $input, callable $next): PromiseInterface
    {
        $headers = $input->nexusHeaders;
        foreach (MDC::getAll() as $key => $value) {
            if (\str_starts_with($key, self::NEXUS_HEADER_PREFIX)) {
                $headers[$key] = $value;
            }
        }

        return $next($input->with(nexusHeaders: $headers));
    }
}
