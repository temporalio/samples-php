<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Mock;

use Temporal\Nexus\Nexus;
use Temporal\Nexus\WorkflowHandle;
use Temporal\Samples\NexusContextPropagation\Handler\SampleNexusServiceImpl;
use Temporal\Samples\Nexus\Service\EchoInput;
use Temporal\Samples\Nexus\Service\EchoOutput;
use Temporal\Samples\Nexus\Service\HelloInput;
use Temporal\Samples\Nexus\Service\SampleNexusService;

/**
 * Test double for the context-propagation contract: `echo` returns the
 * propagated `x-nexus-caller-workflow-id` header instead of the message, so a
 * test can assert the header crossed the Nexus boundary. The production
 * service only logs it. `hello` delegates to the production implementation.
 */
final class HeaderEchoNexusServiceImpl implements SampleNexusService
{
    public const MISSING = '<missing>';

    private readonly SampleNexusServiceImpl $inner;

    public function __construct()
    {
        $this->inner = new SampleNexusServiceImpl();
    }

    public function echo(EchoInput $input): EchoOutput
    {
        $headers = Nexus::getCurrentOperationContext()->headers;

        return new EchoOutput($headers->get('x-nexus-caller-workflow-id') ?? self::MISSING);
    }

    public function hello(HelloInput $input): WorkflowHandle
    {
        return $this->inner->hello($input);
    }
}
