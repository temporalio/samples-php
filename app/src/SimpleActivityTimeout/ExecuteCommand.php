<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SimpleActivityTimeout;

use Carbon\CarbonInterval;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\SampleUtils\Command;

// @@@SNIPSTART php-hello-client
class ExecuteCommand extends Command
{
    protected const NAME = 'simple-activity-timeout';
    protected const DESCRIPTION = 'Execute SimpleActivityTimeout\GreetingWorkflow';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        for ($i = 0; $i < 1000; ++$i) {
            $workflow = $this->workflowClient->newWorkflowStub(
                GreetingWorkflowInterface::class,
                WorkflowOptions::new()
                    ->withWorkflowExecutionTimeout(CarbonInterval::minute())
            );

            $this->workflowClient->start($workflow, 'Antony');
        }

        return self::SUCCESS;
    }
}
// @@@SNIPEND
