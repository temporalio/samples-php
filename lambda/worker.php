<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Temporal\Common\Versioning\VersioningBehavior;
use Temporal\Common\Versioning\WorkerDeploymentVersion;
use Temporal\Samples\Lambda\GreetingActivity;
use Temporal\Samples\Lambda\GreetingWorkflow;
use Temporal\Worker\WorkerDeploymentOptions;
use Temporal\Worker\WorkerOptions;
use Temporal\WorkerFactory;

$taskQueue = (string) \getenv('TEMPORAL_TASK_QUEUE');
$deploymentName = \getenv('TEMPORAL_DEPLOYMENT_NAME');
$buildId = (string) \getenv('TEMPORAL_BUILD_ID');

$options = WorkerOptions::new()
    ->withStickyScheduleToStartTimeout(1)
    ->withMaxConcurrentActivityExecutionSize(2)
    ->withMaxConcurrentWorkflowTaskExecutionSize(10)
    ->withMaxConcurrentLocalActivityExecutionSize(2)
    ->withMaxConcurrentActivityTaskPollers(1)
    ->withDisableEagerActivities(true);

if ($deploymentName !== false && $deploymentName !== '') {
    $options = $options->withDeploymentOptions(
        WorkerDeploymentOptions::new()
            ->withUseVersioning(true)
            ->withVersion(WorkerDeploymentVersion::new($deploymentName, $buildId))
            ->withDefaultVersioningBehavior(VersioningBehavior::Pinned),
    );
}

$factory = WorkerFactory::create();

$factory->newWorker($taskQueue, $options)
    ->registerWorkflowTypes(GreetingWorkflow::class)
    ->registerActivity(GreetingActivity::class);

$factory->run();
