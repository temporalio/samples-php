<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusContextPropagation\Handler;

use Temporal\Samples\NexusContextPropagation\Service\EchoInput;
use Temporal\Samples\NexusContextPropagation\Service\EchoOutput;

class EchoClientImpl implements EchoClient
{
    public function echo(EchoInput $input): EchoOutput
    {
        return new EchoOutput($input->message);
    }
}
