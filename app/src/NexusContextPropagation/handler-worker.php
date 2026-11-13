<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Temporal\Client\ClientOptions;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Samples\NexusContextPropagation\Handler\HandlerWorker;
use Temporal\Samples\NexusContextPropagation\Handler\HelloHandlerWorkflowImpl;
use Temporal\Samples\NexusContextPropagation\Handler\SampleNexusServiceImpl;
use Temporal\Interceptor\SimplePipelineProvider;
use Temporal\Samples\NexusContextPropagation\Propagation\NexusStartContextInterceptor;
use Temporal\Samples\NexusContextPropagation\Propagation\WorkflowInboundContextInterceptor;
use Temporal\WorkerFactory;

ini_set('display_errors', 'stderr');
include "../../vendor/autoload.php";

$address = \getenv('TEMPORAL_ADDRESS')
    ?: \getenv('TEMPORAL_CLI_ADDRESS')
    ?: ((\getenv('TEMPORAL_HOST') ?: '127.0.0.1') . ':' . (\getenv('TEMPORAL_PORT') ?: '7233'));
$namespace = \getenv('TEMPORAL_NAMESPACE') ?: 'my-target-namespace';

$workflowClient = WorkflowClient::create(
    ServiceClient::create($address),
    (new ClientOptions())->withNamespace($namespace),
    interceptorProvider: new SimplePipelineProvider([new NexusStartContextInterceptor()]),
);

$factory = WorkerFactory::create(client: $workflowClient);

$worker = $factory->newWorker(
    HandlerWorker::TASK_QUEUE,
    interceptorProvider: new SimplePipelineProvider([new WorkflowInboundContextInterceptor()]),
);
$worker->registerWorkflowTypes(HelloHandlerWorkflowImpl::class);
$worker->registerNexusServiceImplementation(new SampleNexusServiceImpl());

$factory->run();
