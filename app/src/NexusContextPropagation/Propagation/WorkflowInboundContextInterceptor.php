<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Propagation;

use Temporal\Interceptor\Trait\WorkflowInboundCallsInterceptorTrait;
use Temporal\Interceptor\WorkflowInbound\WorkflowInput;
use Temporal\Interceptor\WorkflowInboundCallsInterceptor;

final class WorkflowInboundContextInterceptor implements WorkflowInboundCallsInterceptor
{
    use WorkflowInboundCallsInterceptorTrait;

    private const NEXUS_HEADER_PREFIX = 'x-nexus-';

    public function execute(WorkflowInput $input, callable $next): void
    {
        MDC::clear();

        foreach ($input->header as $name => $_) {
            $name = (string) $name;
            if (\str_starts_with($name, self::NEXUS_HEADER_PREFIX)) {
                MDC::put($name, (string) $input->header->getValue($name, 'string'));
            }
        }

        $next($input);
    }
}
