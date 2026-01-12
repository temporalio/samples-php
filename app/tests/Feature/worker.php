<?php

declare(strict_types=1);

use Temporal\Testing\WorkerFactory;

ini_set('display_errors', 'stderr');

chdir(__DIR__ . '/../..');
require_once 'vendor/autoload.php';

$workerFactory = WorkerFactory::create();

$worker = $workerFactory->newWorker(taskQueue: 'tests');

// make sure to register concrete workflow implementations
$worker->registerWorkflowTypes(\Temporal\Samples\SimpleActivity\GreetingWorkflow::class);
$worker->registerActivity(
    \Temporal\Samples\SimpleActivity\GreetingActivity::class,
    fn() => new \Temporal\Samples\SimpleActivity\GreetingActivity(),
);

$workerFactory->run();
