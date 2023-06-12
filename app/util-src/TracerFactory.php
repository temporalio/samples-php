<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\SampleUtils;

use OpenTelemetry\API\Common\Signal\Signals;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\Contrib\Grpc\GrpcTransportFactory;
use OpenTelemetry\Contrib\Otlp\OtlpUtil;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use Temporal\OpenTelemetry\Tracer;
use OpenTelemetry\SDK\Trace;

final class TracerFactory
{
    public static function create(): Tracer
    {
        $endpoint = getenv('OTEL_COLLECTOR_ENDPOINT');
        if (empty($endpoint)) {
            $endpoint = 'http://collector:4317';
        }

        $transport = (new GrpcTransportFactory())->create($endpoint . OtlpUtil::method(Signals::TRACE));
        $spanProcessor = (new Trace\SpanProcessorFactory())->create(new SpanExporter($transport));

        return new Tracer(
            (new Trace\TracerProvider($spanProcessor))->getTracer('Temporal Samples'),
            TraceContextPropagator::getInstance()
        );
    }
}
