<?php

declare(strict_types=1);

namespace Temporal\Samples\InfrequentPolling;

use Temporal\Activity;
use Temporal\Exception\Failure\ApplicationErrorCategory;
use Temporal\Exception\Failure\ApplicationFailure;

class TestService
{
    private static array $attempts = [];
    private const ERROR_ATTEMPTS = 5;

    public function getServiceResult(string $greeting, string $name): string
    {
        $workflowId = Activity::getInfo()->workflowExecution->getID();
        if (!isset(self::$attempts[$workflowId])) {
            self::$attempts[$workflowId] = 0;
        }
        self::$attempts[$workflowId]++;

        echo sprintf(
            "Attempt %d of %d to invoke service\n",
            self::$attempts[$workflowId],
            self::ERROR_ATTEMPTS
        );

        if (self::$attempts[$workflowId] === self::ERROR_ATTEMPTS) {
            return sprintf('%s, %s!', $greeting, $name);
        }

        throw new ApplicationFailure(
            message: 'service is down',
            type: 'ServiceError',
            nonRetryable: false,
            category: ApplicationErrorCategory::Benign,
        );
    }
}
