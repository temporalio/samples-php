<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Interceptors\Interceptor;

use React\Promise\PromiseInterface;
use ReflectionAttribute;
use Temporal\Interceptor\Trait\WorkflowOutboundCallsInterceptorTrait;
use Temporal\Interceptor\WorkflowOutboundCalls\ExecuteActivityInput;
use Temporal\Interceptor\WorkflowOutboundCallsInterceptor;
use Temporal\Samples\Interceptors\Attribute;
use Temporal\Samples\Interceptors\Attribute\ActivityOption;

/**
 * The interceptor is used to set activity options based on attributes that are
 * implement {@see ActivityOption} interface.
 */
final class ActivityAttributesInterceptor implements WorkflowOutboundCallsInterceptor
{
    use WorkflowOutboundCallsInterceptorTrait;

    public function executeActivity(ExecuteActivityInput $input, callable $next): PromiseInterface
    {
        if ($input->method === null) {
            return $next($input);
        }

        $options = $input->options;

        foreach ($this->iterateOptions($input->method) as $attribute) {
            if ($attribute instanceof Attribute\StartToCloseTimeout) {
                \error_log(\sprintf('Redeclare start_to_close timeout of %s to %s', $input->type, $attribute->timeout));
                $options = $options->withStartToCloseTimeout($attribute->timeout);
            }
        }

        return $next($input->with(options: $options));
    }

    /**
     * @return iterable<int, ActivityOption>
     */
    private function iterateOptions(\ReflectionMethod $method): iterable
    {
        $class = $method->getDeclaringClass();
        foreach ($class->getAttributes(Attribute\ActivityOption::class, ReflectionAttribute::IS_INSTANCEOF) as $attr) {
            yield $attr->newInstance();
        }

        foreach ($method->getAttributes(Attribute\ActivityOption::class, ReflectionAttribute::IS_INSTANCEOF) as $attr) {
            yield $attr->newInstance();
        }
    }
}
