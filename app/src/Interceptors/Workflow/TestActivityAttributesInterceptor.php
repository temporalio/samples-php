<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Interceptors\Workflow;

use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Exception\Failure\ActivityFailure;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Samples\Interceptors\Activity\Sleeper;
use Temporal\Samples\Interceptors\Attribute\StartToCloseTimeout;
use Temporal\Samples\Interceptors\Interceptor\ActivityAttributesInterceptor;
use Temporal\Workflow;

#[Workflow\WorkflowInterface]
class TestActivityAttributesInterceptor
{
    private ActivityProxy|Sleeper $sleeper;

    public function __construct()
    {
        $this->sleeper = Workflow::newActivityStub(
            Sleeper::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(10)
                ->withRetryOptions(
                    RetryOptions::new()->withMaximumAttempts(1)
                )
        );
    }

    #[Workflow\WorkflowMethod('Interceptors.TestActivityAttributesInterceptor')]
    public function execute()
    {
        // Sleep for 1 second.
        yield $this->sleeper->sleep(1);

        /**
         * Sleep for 7 seconds. Should fail if the {@see ActivityAttributesInterceptor} is working because there
         * is {@see StartToCloseTimeout} attribute on the {@see Sleeper} class with 5-seconds timeout.
         */
        try {
            yield $this->sleeper->sleep(7);
            throw new ApplicationFailure("ActivityAttributesInterceptor doesn't work on the class", 'TEST', true);
        } catch (ActivityFailure) {
            // OK
        }


        /**
         * Sleep for 3 seconds. Should fail if the {@see ActivityAttributesInterceptor} is working because there
         * is {@see StartToCloseTimeout} attribute on the {@see Sleeper::sleep2()} method with 2-seconds timeout.
         */
        try {
            yield $this->sleeper->sleep2(3);
            throw new ApplicationFailure("ActivityAttributesInterceptor doesn't work on the method", 'TEST', true);
        } catch (ActivityFailure) {
            // OK
        }

        return "OK";
    }
}
