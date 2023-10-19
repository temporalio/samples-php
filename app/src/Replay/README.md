# Replay example

This sample demonstrates using the replay feature to replay a workflow execution.

First run any other example to generate some history. Then run this example to replay the history.

To replay all workflows that were executed in the last 30 minutes run the following command form the root:

```bash
php ./app/app.php replay
```

You can also specify a custom time range and workflow type:

```bash
php ./app/app.php replay MoneyBatch -t 60
```
