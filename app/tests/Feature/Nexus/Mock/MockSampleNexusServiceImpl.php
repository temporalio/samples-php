<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Mock;

use Temporal\Client\WorkflowOptions;
use Temporal\Nexus\Nexus;
use Temporal\Nexus\WorkflowHandle;
use Temporal\Samples\Nexus\Service\EchoInput;
use Temporal\Samples\Nexus\Service\EchoOutput;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\SampleNexusService;

/**
 * Reimplements the Nexus contract from scratch — useful when the production
 * service implementation is unavailable to the test (for example, it lives
 * in a downstream package). Echoes a canned string and routes `hello` to
 * {@see MockHelloHandlerWorkflowImpl} so the async-op wire still resolves
 * end-to-end.
 */
final class MockSampleNexusServiceImpl implements SampleNexusService
{
    public const ECHO_CANNED = 'echo response from service mock';

    public function echo(EchoInput $input): EchoOutput
    {
        return new EchoOutput(self::ECHO_CANNED);
    }

    public function hello(HelloInput $input): WorkflowHandle
    {
        return WorkflowHandle::fromWorkflowMethod(
            MockHelloHandlerWorkflowImpl::class,
            WorkflowOptions::new()->withWorkflowId(Nexus::getStartDetails()->requestId),
            $input,
        );
    }
}
