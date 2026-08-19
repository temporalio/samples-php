<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Temporal\Samples\NexusManualOperation\Handler\HandlerWorker;
use Temporal\Samples\NexusManualOperation\Handler\SampleNexusService;
use Temporal\WorkerFactory;

ini_set('display_errors', 'stderr');
include "../../vendor/autoload.php";

$factory = WorkerFactory::create();

$worker = $factory->newWorker(HandlerWorker::TASK_QUEUE);
$worker->registerNexusServiceImplementation(new SampleNexusService());

$factory->run();
