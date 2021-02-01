<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Query;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

/** Demonstrates query capability. Requires a local instance of Temporal server to be running. */
class QueryWorkflow implements QueryWorkflowInterface
{
    private string $message = '';

    public function createGreeting(string $name)
    {
        $this->message = sprintf('Hello, %s!', $name);

        // Always use Workflow::timer() instead of native sleep() and usleep() functions
        yield Workflow::timer(CarbonInterval::seconds(2));

        $this->message = sprintf('Bye, %s!', $name);
    }

    public function queryGreeting(): string
    {
        return $this->message;
    }
}