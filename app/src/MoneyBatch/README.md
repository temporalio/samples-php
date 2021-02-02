# MoneyBatch sample

This sample demonstrates a situation when a single deposit should be initiated for multiple withdrawals.
For example, when a seller wants to be paid once per fixed number of transactions.
The sample can be easily extended to perform a payment based on a more complex criteria like a specific time or accumulated amount.

The sample also demonstrates how to start a Workflow with a Signal.
If the Workflow is already running it just receives the Signal.
If the Workflow is not running then it is started first and then the Signal is delivered to the Workflow.
You can think about "Signal with start" as a lazy way to create Workflows when signaling them.

From the root of the project, run the following command to start the Workflow:

```bash
php ./app/app.php money-batch
```
