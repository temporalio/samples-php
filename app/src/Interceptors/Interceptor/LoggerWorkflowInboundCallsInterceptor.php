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
use Temporal\Interceptor\Trait\WorkflowInboundCallsInterceptorTrait;
use Temporal\Interceptor\WorkflowInbound\QueryInput;
use Temporal\Interceptor\WorkflowInbound\SignalInput;
use Temporal\Interceptor\WorkflowInbound\UpdateInput;
use Temporal\Interceptor\WorkflowInbound\WorkflowInput;
use Temporal\Interceptor\WorkflowInboundCallsInterceptor;

final class LoggerWorkflowInboundCallsInterceptor implements WorkflowInboundCallsInterceptor
{
    use WorkflowInboundCallsInterceptorTrait;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(WorkflowInput $input, callable $next): void
    {
        $input->info->isReplaying or $this->log(
            "Executing workflow {$input->info->type->name}",
            [
                'workflowId' => $input->info->execution->getID(),
                'runId' => $input->info->execution->getRunID(),
                'headers' => $input->header,
                'arguments' => $input->arguments->getValues(),
            ],
        );

        $next($input);
    }

    public function handleSignal(SignalInput $input, callable $next): void
    {
        $input->info->isReplaying or $this->log(
            "Handling signal {$input->info->type->name}->{$input->signalName}",
            [
                'workflowId' => $input->info->execution->getID(),
                'runId' => $input->info->execution->getRunID(),
                'headers' => $input->header,
                'arguments' => $input->arguments->getValues(),
            ],
        );

        $next($input);
    }

    public function handleQuery(QueryInput $input, callable $next): mixed
    {
        $this->log(
            "Handling query {$input->info->type->name}->{$input->queryName}",
            [
                'workflowId' => $input->info->execution->getID(),
                'runId' => $input->info->execution->getRunID(),
                'arguments' => $input->arguments->getValues(),
            ],
        );

        return $next($input);
    }

    public function handleUpdate(UpdateInput $input, callable $next): mixed
    {
        $input->info->isReplaying or $this->log(
            "Handling update {$input->info->type->name}->{$input->updateName}",
            [
                'workflowId' => $input->info->execution->getID(),
                'runId' => $input->info->execution->getRunID(),
                'updateId' => $input->updateId,
                'headers' => $input->header,
                'arguments' => $input->arguments->getValues(),
            ],
        );

        return $next($input);
    }

    public function validateUpdate(UpdateInput $input, callable $next): void
    {
        $next($input);
    }

    private function log(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }
}
