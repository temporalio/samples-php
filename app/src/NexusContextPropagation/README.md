# Nexus Context Propagation sample

Demonstrates carrying a value from a caller workflow all the way into the
workflow that backs an async Nexus operation. Four pieces, all in
`Propagation/`:

| Piece | Role |
|---|---|
| `MDC` | per-workflow bag of `x-nexus-*` values |
| `NexusOutboundContextInterceptor` | caller side: MDC → operation headers |
| `NexusStartContextInterceptor` | handler side: operation headers → start header of the backing workflow |
| `WorkflowInboundContextInterceptor` | backing workflow: start header → MDC |

`NexusStartContextInterceptor` is a `WorkflowClientCallsInterceptor` registered
on the handler worker's `WorkflowClient`, which is the client the SDK uses to
start the backing workflow — the same shape as `ContextPropagator` in the Java
and Go samples. Nothing is propagated unless you register it: the SDK never
copies Nexus headers into a workflow by itself.

The service contract, the caller workflow interfaces and the handler
`EchoClient` come from the [Nexus sample](../Nexus/README.md); only
`Handler/HelloHandlerWorkflow` stays local, so this sample's handler workflow
can be registered next to the base one in the shared feature-test worker. The
caller
implementations mirror it with one addition — the `MDC::put(...)` line.
`SampleNexusServiceImpl` logs the
propagated workflow ID, and `HelloHandlerWorkflowImpl` appends it to the
greeting, so the value is observable at both boundaries.

> **⚠️ Do not put secrets into Nexus headers.** Nexus header values are
> plain strings on the wire. They bypass the workflow data-converter (the
> hook used by the encryption sample to encrypt payloads end-to-end) and
> are not routed through the gRPC proxy / codec server, so anything you
> stash here will land verbatim in handler-side logs and the Temporal Web
> UI. Use them only for trace IDs, tenant IDs, correlation IDs, and other
> non-sensitive metadata.

## Prerequisites

Beyond the usual (`./temporal`, `./rr`), and the two namespaces the
[Nexus sample](../Nexus/README.md) creates:

```bash
./temporal operator nexus endpoint create \
  --name my-context-propagation-nexus-endpoint \
  --target-namespace my-target-namespace \
  --target-task-queue my-context-propagation-handler-task-queue
```

This sample owns its endpoint, task queues and RoadRunner RPC ports, so it
can run alongside the other Nexus samples.

## Run

Three terminals.

**Handler worker** (`my-target-namespace`):

```bash
cd app/src/NexusContextPropagation
TEMPORAL_NAMESPACE=my-target-namespace ../../rr serve -c .rr.handler.yaml
```

**Caller worker** (`my-caller-namespace`):

```bash
cd app/src/NexusContextPropagation
TEMPORAL_NAMESPACE=my-caller-namespace ../../rr serve -c .rr.caller.yaml
```

**Starter**:

```bash
php app/app.php nexus-context-propagation
```

Caller side:

```
Starting EchoCallerWorkflow...
Started: WorkflowID=... RunID=...
Result: Nexus Echo 👋

Starting HelloCallerWorkflow...
Started: WorkflowID=... RunID=...
Result: Hello Nexus 👋
```

Handler side (in the rr stderr stream):

```
Echo called from a workflow with ID : <caller workflow id>
HelloHandlerWorkflow called from a workflow with ID : <caller workflow id>
```
