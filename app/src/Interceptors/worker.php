<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Temporal\Interceptor\SimplePipelineProvider;
use Temporal\Samples\Interceptors\Activity\Sleeper;
use Temporal\Samples\Interceptors\Interceptor\ActivityAttributesInterceptor;
use Temporal\Samples\Interceptors\Interceptor\RequestLoggerInterceptor;
use Temporal\Samples\Interceptors\Workflow\TestActivityAttributesInterceptor;
use Temporal\WorkerFactory;

ini_set('display_errors', 'stderr');
include "../../vendor/autoload.php";

$factory = WorkerFactory::create();

$worker = $factory->newWorker(taskQueue: 'interceptors', interceptorProvider: new SimplePipelineProvider([
    new RequestLoggerInterceptor(new \Temporal\SampleUtils\Logger()),
    new ActivityAttributesInterceptor()
]))
    ->registerWorkflowTypes(TestActivityAttributesInterceptor::class)
    ->registerActivityImplementations(new Sleeper());

$factory->run();

