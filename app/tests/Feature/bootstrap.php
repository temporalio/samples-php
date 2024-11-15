<?php

declare(strict_types=1);

use Temporal\Testing\Environment;

ini_set('display_errors', 'stderr');
chdir(__DIR__ . '/../..');
require_once 'vendor/autoload.php';

$environment = Environment::create();

$environment->startTemporalTestServer();
$environment->startRoadRunner(
    rrCommand: 'rr serve -c .rr.test.yaml -w tests/Feature',
    commandTimeout: 5
);

register_shutdown_function(fn() => $environment->stop());
