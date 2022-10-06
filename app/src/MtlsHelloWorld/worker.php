<?php

declare(strict_types=1);

use Temporal\Samples\MtlsHelloWorld\GreetingActivity;
use Temporal\Samples\MtlsHelloWorld\GreetingWorkflow;

ini_set('display_errors', 'stderr');
require __DIR__ . '/../../vendor/autoload.php';

$factory = Temporal\WorkerFactory::create();
$worker = $factory->newWorker();

$worker->registerWorkflowTypes(GreetingWorkflow::class);
$worker->registerActivity(GreetingActivity::class);

$factory->run();
