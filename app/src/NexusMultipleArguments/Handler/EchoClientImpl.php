<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Handler;

use Temporal\Samples\NexusMultipleArguments\Service\EchoInput;
use Temporal\Samples\NexusMultipleArguments\Service\EchoOutput;

class EchoClientImpl implements EchoClient
{
    public function echo(EchoInput $input): EchoOutput
    {
        return new EchoOutput($input->message);
    }
}
