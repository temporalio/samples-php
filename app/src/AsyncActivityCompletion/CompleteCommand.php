<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\AsyncActivityCompletion;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\SampleUtils\Command;

class CompleteCommand extends Command
{
    protected const NAME = 'user-activity:complete';
    protected const DESCRIPTION = 'Complete AsyncActivityCompletion\GreetingWorkflow using activity token and user message';

    protected const ARGUMENTS = [
        ['token', InputArgument::REQUIRED, 'Activity token'],
        ['message', InputArgument::REQUIRED, 'Activity token'],
    ];

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // @@@SNIPSTART samples-php-async-activity-completion-completebytoken
        $client = $this->workflowClient->newActivityCompletionClient();
        // Complete the Activity.
        $client->completeByToken(
            base64_decode($input->getArgument('token')),
            $input->getArgument('message')
        );
        // @@@SNIPEND
        $output->writeln("Done.");

        return self::SUCCESS;
    }
}
