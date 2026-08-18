<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusCancellation\Caller;

final class CallerWorker
{
    public const TASK_QUEUE = 'my-cancellation-caller-task-queue';
    public const ENDPOINT_NAME = 'my-cancellation-nexus-endpoint';
}
