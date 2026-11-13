<?php

declare(strict_types=1);

namespace Temporal\Samples\Nexus\Handler;

use Temporal\Samples\Nexus\Service\EchoInput;
use Temporal\Samples\Nexus\Service\EchoOutput;

class EchoClientImpl implements EchoClient
{
    public function echo(EchoInput $input): EchoOutput
    {
        return new EchoOutput($input->message);
    }
}
