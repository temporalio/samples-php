# Updatable Timer Sample
Demonstrates a helper class which relies on Workflow::await to implement a blocking sleep that can be updated
at any moment.

The sample is composed of the three executables:

* `DynamicSleepWorkflow` hosts the workflow code
* `ExecuteCommand` starts a workflow instance.
* `ProlongCommand` signals the workflow instance with the new time to wake up