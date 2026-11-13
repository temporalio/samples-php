# StandaloneActivity sample

This sample executes an Activity directly from a client, without a Workflow ("standalone activity").
It also shows the `listExecutions` and `countExecutions` APIs of the `ActivityClient`.

Standalone activities require Temporal Server 1.31+ with the dynamic config `frontend.activityAPIsEnabled=true`.
Start a local server with:

```bash
temporal server start-dev --dynamic-config-value frontend.activityAPIsEnabled=true
```

From the root of the project, run the following command:

```bash
php ./app/app.php standalone-activity
```

Expected output:

```
Started: ActivityID=standalone_activity_helloworld_ActivityID, RunID=019c8c8f-324f-7c06-a92e-a9f7e612ce69
Result:
Hello Temporal!
Activities:
	ActivityID: standalone_activity_helloworld_ActivityID, Type: StandaloneActivity.ComposeGreeting, Status: Completed
Total activities: 1
```
