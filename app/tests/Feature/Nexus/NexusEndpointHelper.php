<?php

declare(strict_types=1);

namespace App\Tests\Feature\Nexus;

use Temporal\Api\Nexus\V1\EndpointSpec;
use Temporal\Api\Nexus\V1\EndpointTarget;
use Temporal\Api\Nexus\V1\EndpointTarget\Worker as WorkerTarget;
use Temporal\Api\Operatorservice\V1\CreateNexusEndpointRequest;
use Temporal\Api\Operatorservice\V1\DeleteNexusEndpointRequest;
use Temporal\Api\Operatorservice\V1\OperatorServiceClient;

/**
 * Thin gRPC OperatorService wrapper for creating/deleting Nexus endpoints
 * inside Feature tests. Mirrors the spirit of sdk-php's
 * `tests/Acceptance/Extra/Nexus/NexusEndpoints`, simplified for samples.
 */
final class NexusEndpointHelper
{
    private OperatorServiceClient $operator;

    public function __construct(string $temporalAddress)
    {
        $this->operator = new OperatorServiceClient(
            $temporalAddress,
            ['credentials' => \Grpc\ChannelCredentials::createInsecure()],
        );
    }

    /**
     * Create a Nexus endpoint targeting `(namespace, taskQueue)` and return
     * `[id, name]`. The id is used by HTTP routes, the name is what callers
     * pass to {@see \Temporal\Workflow\NexusOperationOptions::withEndpoint()}.
     *
     * @return array{id: string, name: string}
     */
    public function setupEndpoint(string $namespace, string $taskQueue, string $prefix = 'samples-test'): array
    {
        $name = $prefix . '-' . \bin2hex(\random_bytes(4));

        $request = (new CreateNexusEndpointRequest())
            ->setSpec(
                (new EndpointSpec())
                    ->setName($name)
                    ->setTarget(
                        (new EndpointTarget())->setWorker(
                            (new WorkerTarget())
                                ->setNamespace($namespace)
                                ->setTaskQueue($taskQueue),
                        ),
                    ),
            );

        [$response, $status] = $this->operator->CreateNexusEndpoint($request)->wait();

        if ($status->code !== \Grpc\STATUS_OK) {
            throw new \RuntimeException(
                "CreateNexusEndpoint failed (gRPC code {$status->code}): {$status->details}",
            );
        }

        // Give the frontend's endpoint registry a moment to pick up the new
        // endpoint before the first call routes through it.
        \usleep(100_000);

        return ['id' => $response->getEndpoint()->getId(), 'name' => $name];
    }

    public function deleteEndpoint(string $endpointId, int $expectedVersion = 1): void
    {
        $request = (new DeleteNexusEndpointRequest())
            ->setId($endpointId)
            ->setVersion($expectedVersion);

        [, $status] = $this->operator->DeleteNexusEndpoint($request)->wait();

        if ($status->code !== \Grpc\STATUS_OK) {
            // Best-effort cleanup; surface, don't crash other tests.
            \trigger_error(
                "DeleteNexusEndpoint failed (gRPC code {$status->code}): {$status->details}",
                E_USER_WARNING,
            );
        }
    }
}
