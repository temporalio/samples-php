<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Temporal\Interceptor\SimplePipelineProvider;
use Temporal\Samples\NexusContextPropagation\Caller\CallerWorker;
use Temporal\Samples\NexusContextPropagation\Caller\EchoCallerWorkflowImpl;
use Temporal\Samples\NexusContextPropagation\Caller\HelloCallerWorkflowImpl;
use Temporal\Samples\NexusContextPropagation\Propagation\NexusOutboundContextInterceptor;
use Temporal\WorkerFactory;

ini_set('display_errors', 'stderr');
include "../../vendor/autoload.php";

$factory = WorkerFactory::create();

$factory->newWorker(
    CallerWorker::TASK_QUEUE,
    interceptorProvider: new SimplePipelineProvider([
        new NexusOutboundContextInterceptor(),
    ]),
)->registerWorkflowTypes(
    EchoCallerWorkflowImpl::class,
    HelloCallerWorkflowImpl::class,
);

$factory->run();
