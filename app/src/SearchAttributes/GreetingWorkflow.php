<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SearchAttributes;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Workflow;

class GreetingWorkflow implements GreetingWorkflowInterface
{
    /** @var GreetingActivityInterface */
    private $greetingsActivity;

    public function __construct()
    {
        $this->greetingsActivity = Workflow::newActivityStub(
            GreetingActivityInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(2))
        );
    }

    public function getGreeting(string $name)
    {
        Workflow::upsertSearchAttributes(
            [
                'CustomKeywordField' => 'attr1-value',
                'CustomIntField' => 123,
            ]
        );

        return yield $this->greetingsActivity->composeGreeting('Hello', $name);
    }
}
