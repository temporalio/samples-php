<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SimpleActivityException;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\SampleUtils\Command;

// @@@SNIPSTART php-hello-client
class ExecuteCommand extends Command
{
    protected const NAME = 'simple-activity-exception';
    protected const DESCRIPTION = 'Execute SimpleActivityException\GreetingWorkflow';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        for ($i = 0; $i < 1000; ++$i) {
            $workflow = $this->workflowClient->newWorkflowStub(
                GreetingWorkflowInterface::class,
            );

            $this->workflowClient->start($workflow, 'Antony');
        }

        return self::SUCCESS;
    }
}
// @@@SNIPEND
