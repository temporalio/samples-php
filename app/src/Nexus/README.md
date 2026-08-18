# Nexus sample

A caller workflow in `my-caller-namespace` invokes operations on a Nexus
service hosted by a handler worker in `my-target-namespace`. Two operations:

- `echo` — synchronous (`#[Operation]`)
- `hello` — async, `WorkflowRunOperation`, starts `HelloHandlerWorkflow`
  server-side (`#[AsyncOperation(output: HelloOutput::class)]`)

Two caller workflows:

- `EchoCallerWorkflow` — typed stub, sync op.
- `HelloCallerWorkflow` — untyped stub via
  `Workflow::newUntypedNexusOperationStub()`. `start()` resolves with a
  `NexusOperationHandle` that exposes `operationToken` (string for async,
  null for sync) before `getResult()` resolves with the typed result.

## Prerequisites

Beyond the usual (`./temporal`, `./rr`):

```bash
./temporal operator namespace create --namespace my-target-namespace
./temporal operator namespace create --namespace my-caller-namespace

./temporal operator nexus endpoint create \
  --name my-nexus-endpoint-name \
  --target-namespace my-target-namespace \
  --target-task-queue my-handler-task-queue \
  --description-file ./app/src/Nexus/Service/description.md
```

## Run

Three terminals.

**Handler worker** (`my-target-namespace`):

```bash
cd app/src/Nexus
TEMPORAL_NAMESPACE=my-target-namespace ../../rr serve -c .rr.handler.yaml
```

**Caller worker** (`my-caller-namespace`):

```bash
cd app/src/Nexus
TEMPORAL_NAMESPACE=my-caller-namespace ../../rr serve -c .rr.caller.yaml
```

**Starter**:

```bash
php app/app.php nexus
```

Expected output:

```
Starting EchoCallerWorkflow...
Started: WorkflowID=... RunID=...
Result: Nexus Echo 👋

Starting HelloCallerWorkflow...
Started: WorkflowID=... RunID=...
Result: ¡Hola! Nexus 👋
```

## Constants

| Name | Value |
|---|---|
| service | `SampleNexusService` |
| operations | `echo`, `hello` |
| endpoint | `my-nexus-endpoint-name` |
| handler task queue | `my-handler-task-queue` |
| caller task queue | `my-caller-workflow-task-queue` |
| target namespace | `my-target-namespace` |
| caller namespace | `my-caller-namespace` |
| `scheduleToCloseTimeout` | `10s` |
