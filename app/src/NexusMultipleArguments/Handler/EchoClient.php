<?php

declare(strict_types=1);

namespace Temporal\Samples\NexusMultipleArguments\Handler;

use Temporal\Samples\NexusMultipleArguments\Service\EchoInput;
use Temporal\Samples\NexusMultipleArguments\Service\EchoOutput;

interface EchoClient
{
    public function echo(EchoInput $input): EchoOutput;
}
