# Atomic message handlers

This sample shows off important techniques for handling signals and updates, aka messages. In particular, it illustrates how message handlers can interleave or not be completed before the workflow completes, and how you can manage that.

* Here, using `Workflow::await()`, signal and update handlers will only operate when the workflow is within a certain state--between `cluster_started` and `cluster_shutdown`.
* You can run the Workflow with an initializer signal that you want to run before anything else other than the workflow's constructor.
  This pattern is known as "signal-with-start."
* Message handlers can block and their actions can be interleaved with one another and with the main Workflow.
  This can easily cause bugs, so you can use a Mutex object to protect shared state from interleaved access.
* An "Entity" workflow, i.e. a long-lived workflow, periodically "continues as new".  It must do this to prevent its history from growing too large, and it passes its state to the next workflow.
  You can check `Workflow::getInfo()->shouldContinueAsNew` to see when it's time.
* Most people want their message handlers to finish before the workflow run completes or continues as new.
  Use `yield Workflow::await(fn() => Workflow::allHandlersFinished())` to achieve this.
* Message handlers can be made idempotent.
  See update `MessageHandlerWorkflow::assignNodesToJob()`.


From the root of the project, run the following command:

```bash
php ./app/app.php safe-message-handlers
```
