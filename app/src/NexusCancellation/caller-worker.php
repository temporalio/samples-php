<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Temporal\Samples\NexusCancellation\Caller\CallerWorker;
use Temporal\Samples\NexusCancellation\Caller\HelloCallerWorkflowImpl;
use Temporal\WorkerFactory;

ini_set('display_errors', 'stderr');
include "../../vendor/autoload.php";

$factory = WorkerFactory::create();

$factory->newWorker(CallerWorker::TASK_QUEUE)
    ->registerWorkflowTypes(HelloCallerWorkflowImpl::class);

$factory->run();
