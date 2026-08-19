# Nexus Cancellation sample

A caller workflow fans out the `hello` Nexus operation in 5 languages in
parallel. After the first reply arrives it cancels the cancellation scope —
the other 4 in-flight operations get cancellation requests, the handler
workflows clean up and rethrow `CanceledFailure`, and the caller drains the
promises (ignoring `CanceledFailure`) before returning the first result.

Reuses the service contract and `SampleNexusServiceImpl` of the
[Nexus sample](../Nexus/README.md); the only sample-specific handler code is
`HelloHandlerWorkflowImpl`, which adds a random delay and a detached cleanup
scope.

Caller passes `NexusOperationCancellationType::WaitRequested` so it returns
as soon as the handler acknowledges the cancellation request — it doesn't
wait for the detached cleanup scope to finish on the handler side.

## Prerequisites

Beyond the usual (`./temporal`, `./rr`), and the two namespaces the
[Nexus sample](../Nexus/README.md) creates:

```bash
./temporal operator nexus endpoint create \
  --name my-cancellation-nexus-endpoint \
  --target-namespace my-target-namespace \
  --target-task-queue my-cancellation-handler-task-queue
```

This sample owns its endpoint, task queues and RoadRunner RPC ports, so it
can run alongside the other Nexus samples.

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
