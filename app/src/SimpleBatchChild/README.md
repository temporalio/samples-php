# Simple batch with child workflows sample

This sample demonstrates a simple batch processing, with child workflows.

Unlike the `SimpleBatch` sample, using child workflows will prevent from exceeding [the workflow history limits](https://docs.temporal.io/self-hosted-guide/defaults).

Run the following command to start a batch with a number of items randomly chosen between given min and max values:

```bash
php ./app/app.php simple-batch-child:start <batchId> [--min <min item count>] [--max <max item count>]
```

The minimum and maximum item count resp. default to 10 and 20.

Run the following command to show the batch status:

```bash
php ./app/app.php simple-batch-child:status <batchId>
```
