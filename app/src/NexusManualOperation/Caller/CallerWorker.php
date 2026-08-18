<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusManualOperation\Caller;

final class CallerWorker
{
    public const TASK_QUEUE = 'my-manual-caller-task-queue';
    public const ENDPOINT_NAME = 'my-manual-nexus-endpoint';
}
