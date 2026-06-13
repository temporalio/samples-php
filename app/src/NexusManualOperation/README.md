# Nexus Manual Operation sample

The third Nexus handler form: a **manual** operation backed by an
`OperationHandlerInterface` object. Unlike `#[Operation]` (sync method) and
the `WorkflowHandle`-returning `#[AsyncOperation]` (SDK-managed workflow run),
here one object owns the whole lifecycle:

- `start()` decides **at runtime** whether to answer synchronously
  (`OperationStartResult::sync(...)` — no token issued) or asynchronously
  (`OperationStartResult::async(new OperationInfo($token, ...))` — the
  handler mints its **own token**, here the external job id).
- `cancel()` receives that same token back via
  `$details->operationToken` and aborts the external job.

The `#[Service]` attribute sits on a self-contained **class** — no interface
is needed, because the operation's wire contract (input/output types) lives
on the `#[AsyncOperation(output: ..., input: ...)]` attribute of the
zero-parameter factory method, which runs once at worker registration. No
`WorkflowClient` is needed either: nothing starts a backing workflow.

Completion of the async result is delivered by the external system via the
Nexus completion callback (`$details->callbackUrl`) and is out of scope here —
the sample demonstrates the sync fast-path, the custom token, and cancel.

The caller (`JobCallerWorkflow`) uses the **untyped** stub
(`Workflow::newUntypedNexusOperationStub()`): call 1 with `instant: true`
returns immediately with a `null` token; call 2 runs async inside a
cancellable scope, the caller reads the handler-issued `job-...` token and
then cancels the scope, which routes to the handler's `cancel()`.

## Prerequisites

Beyond the usual (`./temporal`, `./rr`):

```bash
./temporal operator namespace create --namespace my-target-namespace
./temporal operator namespace create --namespace my-caller-namespace

./temporal operator nexus endpoint create \
  --name my-nexus-endpoint-name \
  --target-namespace my-target-namespace \
  --target-task-queue my-manual-handler-task-queue
```

If `my-nexus-endpoint-name` already exists from the [Nexus sample](../Nexus/README.md),
re-point it instead:

```bash
./temporal operator nexus endpoint update \
  --name my-nexus-endpoint-name \
  --target-namespace my-target-namespace \
  --target-task-queue my-manual-handler-task-queue
```

## Run

Three terminals.

**Handler worker** (`my-target-namespace`):

```bash
cd app/src/NexusManualOperation
TEMPORAL_NAMESPACE=my-target-namespace ../../rr serve -c .rr.handler.yaml
```

**Caller worker** (`my-caller-namespace`):

```bash
cd app/src/NexusManualOperation
TEMPORAL_NAMESPACE=my-caller-namespace ../../rr serve -c .rr.caller.yaml
```

**Starter**:

```bash
php app/app.php nexus-manual-operation
```

Expected output:

```
Starting JobCallerWorkflow...
Started: WorkflowID=... RunID=...
Result: [instant=done instantly: Demo] [token=job-...] cancelled
```

The `token=job-...` part is the handler-minted token (the fake external job
id built from the start request id); the handler worker logs
`ExternalJobClient: aborted job-...` when the cancel arrives.

## Constants

| Name | Value |
|---|---|
| service | `SampleNexusService` |
| operations | `startJob` |
| endpoint | `my-nexus-endpoint-name` |
| handler task queue | `my-manual-handler-task-queue` |
| caller task queue | `my-manual-caller-task-queue` |
| target namespace | `my-target-namespace` |
| caller namespace | `my-caller-namespace` |
| `scheduleToCloseTimeout` | `20s` |
| `cancellationType` | `TryCancel` |
