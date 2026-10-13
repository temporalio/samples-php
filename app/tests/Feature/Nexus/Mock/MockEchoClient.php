<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus\Mock;

use Temporal\Samples\Nexus\Handler\EchoClient;
use Temporal\Samples\Nexus\Service\EchoInput;
use Temporal\Samples\Nexus\Service\EchoOutput;

/**
 * Stub injected into the production {@see \Temporal\Samples\Nexus\Handler\SampleNexusServiceImpl}
 * for the mock-handler test scenario — verifies that DI lets you swap the
 * sync-op dependency without forking the service implementation.
 */
final class MockEchoClient implements EchoClient
{
    public const CANNED = 'mocked echo';

    public function echo(EchoInput $input): EchoOutput
    {
        return new EchoOutput(self::CANNED);
    }
}
