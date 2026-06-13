# Nexus Cancellation sample

A caller workflow fans out the `hello` Nexus operation in 5 languages in
parallel. After the first reply arrives it cancels the cancellation scope —
the other 4 in-flight operations get cancellation requests, the handler
workflows clean up and rethrow `CanceledFailure`, and the caller drains the
promises (ignoring `CanceledFailure`) before returning the first result.

Defines its own single-operation `SampleNexusService` (no `echo`), modeled
on the [Nexus sample](../Nexus/README.md). Its `HelloHandlerWorkflowImpl`
(in `Handler/`) adds a random delay and a detached cleanup scope.

Caller passes `NexusOperationCancellationType::WaitRequested` so it returns
as soon as the handler acknowledges the cancellation request — it doesn't
wait for the detached cleanup scope to finish on the handler side.

## Prerequisites

Same setup as the [Nexus sample](../Nexus/README.md) — namespaces and the
endpoint must already exist. **Stop any regular-Nexus workers first**: both
samples share `my-handler-task-queue` / `my-caller-workflow-task-queue` and
register the same workflow type names.

## Run

Three terminals.

**Handler worker** (`my-target-namespace`):

```bash
cd app/src/NexusCancellation
TEMPORAL_NAMESPACE=my-target-namespace ../../rr serve -c .rr.handler.yaml
```

**Caller worker** (`my-caller-namespace`):

```bash
cd app/src/NexusCancellation
TEMPORAL_NAMESPACE=my-caller-namespace ../../rr serve -c .rr.caller.yaml
```

**Starter**:

```bash
php app/app.php nexus-cancellation
```

Caller side prints the first reply (whichever language won the race):

```
Starting HelloCallerWorkflow...
Started: WorkflowID=... RunID=...
Result: ¡Hola! Nexus 👋
```

Handler side logs each cancelled run:

```
HelloHandlerWorkflow was cancelled successfully.
HelloHandlerWorkflow was cancelled successfully.
HelloHandlerWorkflow was cancelled successfully.
HelloHandlerWorkflow was cancelled successfully.
```
