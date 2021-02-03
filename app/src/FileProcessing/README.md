# FileProcessing sample

This sample demonstrates how to how to use Task routing.

This Workflow downloads a file, processes it, and uploads the result to a destination.
Any worker can pick up the first Activity.
However, the second and third Activities must be executed on the same host as the first one.

The example registers the secondary (host specific) Task Queue in the `worker.php` file:

```php
// We can use task queue for more complex task routing, for example our FileProcessing
// activity will receive unique, host specific, TaskQueue which can be used to process
// files locally.
$hostTaskQueue = gethostname();

$factory->newWorker($hostTaskQueue)
    ->registerActivityImplementations(new FileProcessing\StoreActivity($hostTaskQueue));
```

From the root of the project, run the following command:

```bash
php ./app/app.php process-file {url}
```

Each invocation starts a new Workflow execution.

Example:

```bash
php ./app/app.php process-file https://github.com/temporalio/temporal/archive/v1.6.3.zip
```
