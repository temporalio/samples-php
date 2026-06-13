# Nexus Multiple Arguments sample

Same Nexus contract as the [basic Nexus sample](../Nexus/README.md), but the
backing handler workflow takes **two positional arguments** instead of one
`HelloInput` DTO. The unpack happens in the service implementation: when the
operation receives a `HelloInput`, it passes `$input->name` and
`$input->language` as separate arguments to the workflow via
`WorkflowHandle::fromWorkflowMethod(class, options, ...$args)`.

Useful when the Nexus contract DTO and the underlying workflow signature
have to evolve independently — e.g. the workflow already exists with a
multi-arg signature and is called from many places, and you want to wrap
it behind a Nexus operation without changing the workflow.

Each sample keeps its own copy of `Service/`, `Caller/` and `Handler/`;
nothing is literally shared with the [basic Nexus sample](../Nexus/README.md).
Differences: `Service/` keeps the same shape, `Handler/HelloHandlerWorkflow`
and `Handler/HelloHandlerWorkflowImpl` switch to the multi-arg signature,
and `Handler/SampleNexusServiceImpl` unpacks the input.

## Prerequisites

Same setup as the [basic Nexus sample](../Nexus/README.md) — namespaces and
the endpoint must already exist. **Stop any other Nexus-flavour workers
first**: this sample shares `my-handler-task-queue` /
`my-caller-workflow-task-queue` and registers the same workflow type names
as the basic sample but with a different signature, so co-running them
will cause deserialization failures.

## Run

Three terminals.

**Handler worker** (`my-target-namespace`):

```bash
cd app/src/NexusMultipleArguments
TEMPORAL_NAMESPACE=my-target-namespace ../../rr serve -c .rr.handler.yaml
```

**Caller worker** (`my-caller-namespace`):

```bash
cd app/src/NexusMultipleArguments
TEMPORAL_NAMESPACE=my-caller-namespace ../../rr serve -c .rr.caller.yaml
```

**Starter**:

```bash
php app/app.php nexus-multiple-arguments
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

## What's different from the basic sample

Three files: the handler workflow interface and implementation (multi-arg
signature) and the service implementation (unpacks the DTO). The rest is a
copy of the basic sample.

`Handler/HelloHandlerWorkflow.php` — interface signature uses positional args:

```php
#[WorkflowInterface]
interface HelloHandlerWorkflow
{
    #[WorkflowMethod]
    public function hello(string $name, Language $language);
}
```

`Handler/SampleNexusServiceImpl.php::hello()` — unpacks `HelloInput` when
building the `WorkflowHandle`:

```php
return WorkflowHandle::fromWorkflowMethod(
    HelloHandlerWorkflow::class,
    WorkflowOptions::new()->withWorkflowId(Nexus::getStartDetails()->requestId),
    $input->name,        // ← positional
    $input->language,    // ← positional
);
```

Compare with the basic sample, which passes the whole DTO:

```php
return WorkflowHandle::fromWorkflowMethod(
    HelloHandlerWorkflow::class,
    WorkflowOptions::new()->withWorkflowId(Nexus::getStartDetails()->requestId),
    $input,                  // ← whole DTO, single arg
);
```

The Nexus operation contract (`SampleNexusService::hello(HelloInput): WorkflowHandle`)
and the caller workflows are unchanged.

## Constants

Same as the [basic Nexus sample](../Nexus/README.md). Listed here for
quick reference:

| Name | Value |
|---|---|
| service | `SampleNexusService` |
| operations | `echo`, `hello` |
| endpoint | `my-nexus-endpoint-name` |
| handler task queue | `my-handler-task-queue` |
| caller task queue | `my-caller-workflow-task-queue` |
| target namespace | `my-target-namespace` |
| caller namespace | `my-caller-namespace` |
