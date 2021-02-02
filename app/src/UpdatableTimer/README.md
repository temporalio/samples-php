# UpdatableTimer Sample

This sample demonstrates the use of a helper class which relies on Workflow::await to implement a blocking sleep that can be updated at any moment.

The sample is composed of the three executables:

- `DynamicSleepWorkflow`: Hosts the Workflow code
- `ExecuteCommand`: Starts a Workflow instance
- `ProlongCommand`: Signals the Workflow instance with the new time to wake up

This sample supports two separate commands.
One to start Workflow execution, and the second to send Signals to request timer updates.

**Start Workflow execution**

From the root of the project, run the following command:

```bash
php ./app/app.php updatable-timer:start
```

**Extend timer duration**

From the root of the project, run the following command:

```bash
php ./app/app.php updatable-timer:prolong
```
