<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Caller;

final class CallerWorker
{
    public const TASK_QUEUE = 'my-context-propagation-caller-task-queue';
    public const ENDPOINT_NAME = 'my-context-propagation-nexus-endpoint';
}
