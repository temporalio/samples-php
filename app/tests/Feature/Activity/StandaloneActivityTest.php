<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Feature\Activity;

use App\Tests\Feature\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Temporal\Client\Activity\ActivityExecutionStatus;
use Temporal\Client\Activity\StartActivityOptions;
use Temporal\Client\ActivityClient;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Samples\StandaloneActivity\GreetingActivity;
use Temporal\Samples\StandaloneActivity\GreetingActivityInterface;

#[CoversClass(GreetingActivity::class)]
final class StandaloneActivityTest extends TestCase
{
    public function testExecuteWithoutWorkflow(): void
    {
        $client = ActivityClient::create(ServiceClient::create(\getenv('TEMPORAL_ADDRESS')));

        $handle = $client->start(
            GreetingActivityInterface::TYPE,
            StartActivityOptions::new()
                ->withId(\uniqid('standalone-activity-', true))
                ->withTaskQueue('tests')
                ->withScheduleToCloseTimeout(10),
            'Temporal',
        );

        $this->assertNotSame('', (string) $handle->getRunId());
        $this->assertSame('Hello Temporal!', $handle->getResult('string'));
        $this->assertSame(ActivityExecutionStatus::Completed, $handle->describe()->info->status);
    }
}
