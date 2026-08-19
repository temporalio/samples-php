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
use Temporal\Samples\NexusMultipleArguments\Handler\HandlerWorker;
use Temporal\Samples\NexusMultipleArguments\Handler\HelloHandlerWorkflowImpl;
use Temporal\Samples\NexusMultipleArguments\Handler\SampleNexusServiceImpl;
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
);

$factory = WorkerFactory::create(client: $workflowClient);

$worker = $factory->newWorker(HandlerWorker::TASK_QUEUE);
$worker->registerWorkflowTypes(HelloHandlerWorkflowImpl::class);
$worker->registerNexusServiceImplementation(new SampleNexusServiceImpl());

$factory->run();
