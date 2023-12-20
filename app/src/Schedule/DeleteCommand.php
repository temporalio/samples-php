<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Schedule;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\Schedule;
use Temporal\Common\RetryOptions;
use Temporal\SampleUtils\Command;

class DeleteCommand extends Command
{
    protected const NAME = 'schedule:delete';
    protected const DESCRIPTION = 'Delete scheduled workflow';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $handle = $this->scheduleClient->getHandle(ScheduleWorkflowInterface::SCHEDULE_ID);

        $handle->delete();

        return self::SUCCESS;
    }
}