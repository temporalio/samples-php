# Nexus Context Propagation sample

Demonstrates copying a value from a caller workflow into the Nexus operation
headers, then reading it on the handler side. The caller writes a workflow ID
into a static `MDC` bag, a `WorkflowOutboundCallsInterceptor` copies any
`x-nexus-*` keys onto the operation's nexus headers, and the handler reads
them off `OperationContext::$headers` inside the service implementation.

Each sample keeps its own copy of `Service/`, `Caller/` and `Handler/`. The
caller implementations mirror the [Nexus sample](../Nexus/README.md) with one
addition — the `MDC::put(...)` line. The `SampleNexusServiceImpl` (in
`Handler/`) logs the propagated workflow ID before delegating.

> **Workflow-side propagation gap.** The Java sample also uses
> `MDCContextPropagator` plus a Nexus inbound interceptor to push values into
> the handler workflow's MDC so its body can log them. PHP SDK doesn't yet
> have a `ContextPropagator`, so this port logs only at the service-impl
> boundary (where `OperationContext` is available). Inside the started
> `HelloHandlerWorkflow` the headers are not visible.

> **⚠️ Do not put secrets into Nexus headers.** Nexus header values are
> plain strings on the wire. They bypass the workflow data-converter (the
> hook used by the encryption sample to encrypt payloads end-to-end) and
> are not routed through the gRPC proxy / codec server, so anything you
> stash here will land verbatim in handler-side logs and the Temporal Web
> UI. Use them only for trace IDs, tenant IDs, correlation IDs, and other
> non-sensitive metadata.

## Prerequisites

Same setup as the [Nexus sample](../Nexus/README.md) — namespaces and the
endpoint must already exist. **Stop any other Nexus-flavour workers first**:
this sample shares `my-handler-task-queue` / `my-caller-workflow-task-queue`
and registers the same workflow type names.

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
