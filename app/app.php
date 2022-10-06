<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Temporal\SampleUtils\DeclarationLocator;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Symfony\Component\Console\Application;
use Temporal\SampleUtils\Command;

require __DIR__ . '/vendor/autoload.php';

// finds all available workflows, activity types and commands in a given directory
$declarations = DeclarationLocator::create(__DIR__ . '/src/');

$host = getenv('TEMPORAL_CLI_ADDRESS');
if (empty($host)) {
    $host = 'localhost:7233';
}

$workflowClient = WorkflowClient::create(ServiceClient::create($host));

$app = new Application('Temporal PHP-SDK Samples');

foreach ($declarations->getCommands() as $command) {
    $app->add(Command::create($command, $workflowClient));
}

$app->run();
