<?php

declare(strict_types=1);

use Temporal\Testing\Environment;

ini_set('display_errors', 'stderr');
chdir(__DIR__ . '/../..');
require_once 'vendor/autoload.php';

$environment = Environment::create();

$sysInfo = \Temporal\Testing\SystemInfo::detect();

$environment->startTemporalServer(
    parameters: ['--dynamic-config-value', 'frontend.activityAPIsEnabled=true'],
);
$environment->startRoadRunner(
    rrCommand: [$sysInfo->rrExecutable, 'serve', '-c', '.rr.test.yaml', '-w', 'tests/Feature'],
    commandTimeout: 5,
    configFile: 'tests/Feature/.rr.test.yaml',
);

register_shutdown_function(fn() => $environment->stop());
