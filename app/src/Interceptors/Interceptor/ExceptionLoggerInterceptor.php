<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Interceptors\Interceptor;

use Psr\Log\LoggerInterface;
use React\Promise\PromiseInterface;
use Temporal\Interceptor\WorkflowOutboundRequestInterceptor;
use Temporal\Worker\Transport\Command\RequestInterface;

final class ExceptionLoggerInterceptor implements WorkflowOutboundRequestInterceptor
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function handleOutboundRequest(RequestInterface $request, callable $next): PromiseInterface
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}
