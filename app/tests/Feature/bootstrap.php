<?php

declare(strict_types=1);

use Temporal\Testing\Environment;

ini_set('display_errors', 'stderr');
chdir(__DIR__ . '/../..');
require_once 'vendor/autoload.php';

$environment = Environment::create();

$sysInfo = \Temporal\Testing\SystemInfo::detect();

// Nexus needs the full Temporal server; the time-skipping test server
// shipped via `startTemporalTestServer()` doesn't expose the Nexus APIs.
// Mirrors what sdk-php's acceptance harness does in TemporalStarter.
$environment->startTemporalServer(
    parameters: [
        '--http-port', '7243',
    ],
);
// rr's `-c` is resolved relative to its `-w` workdir, while
// Environment::startRoadRunner's internal readiness check (`rr workers -c …`)
// runs from PHP's cwd. They need different paths.
$environment->startRoadRunner(
    rrCommand: [$sysInfo->rrExecutable, 'serve', '-c', '.rr.test.yaml', '-w', 'tests/Feature'],
    commandTimeout: 5,
    configFile: 'tests/Feature/.rr.test.yaml',
);

register_shutdown_function(fn() => $environment->stop());
