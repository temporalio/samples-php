<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Periodic;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\SampleUtils\Command;

class StopCommand extends Command
{
    protected const NAME = 'periodic:stop';
    protected const DESCRIPTION = 'Stop Periodic\PeriodicWorkflow';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workflow = $this->workflowClient->newUntypedRunningWorkflowStub(
            PeriodicWorkflowInterface::WORKFLOW_ID
        );

        $workflow->cancel();

        return self::SUCCESS;
    }
}