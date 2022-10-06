<?php

declare(strict_types=1);

use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowOptions;
use Temporal\Samples\MtlsHelloWorld\GreetingWorkflow;

require __DIR__ . '/../../vendor/autoload.php';

$workflowClient = Temporal\Client\WorkflowClient::create(
    ServiceClient::createSSL(
        'localhost:7233',
        getenv('TEMPORAL_SERVER_ROOT_CA_CERT_PATH'),
        getenv('TEMPORAL_CLIENT_KEY_PATH'),
        getenv('TEMPORAL_CLIENT_CERT_PATH'),
        getenv('TEMPORAL_SERVER_NAME_OVERRIDE')
    ),
);
$workflow = $workflowClient->newWorkflowStub(
    GreetingWorkflow::class,
    WorkflowOptions::new()->withWorkflowExecutionTimeout(120)
);

echo $workflow->greet('Hello') . PHP_EOL;

//print_r($workflow->query('getStatus')->getValue(0));
