<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Temporal\SampleUtils\DeclarationLocator;
use Temporal\WorkerFactory;

ini_set('display_errors', 'stderr');
include "vendor/autoload.php";

// finds all available workflows, activity types and commands in a given directory
$declarations = DeclarationLocator::create(__DIR__ . '/src/');

// factory initiates and runs task queue specific activity and workflow workers
$factory = WorkerFactory::create();

$worker = $factory->newWorker();

foreach ($declarations->getWorkflowTypes() as $workflowType) {
    // by class name
    $worker->registerWorkflowTypes($workflowType);
}

foreach ($declarations->getActivityTypes() as $activityType) {
    // by class name (resolved via associated activity factory)
    $worker->registerActivityImplementations(new $activityType());
}

// todo: local task queue (ОЧЕНЬ ВНЯТНЫЙ КОМЕНТ)

// todo: make better comment
$factory->run();