# Cron sample

The example demonstrates how you can interact with the Temporal Schedule API.  
A Workflow that simply greets the user in different languages will be scheduled and executed.
The language is determined before creating the Schedule and is passed through the headers.  
The main code for interacting with the Schedule API is located in console commands, of which there are several:

- `schedule:create` - schedules the execution of the Workflow with a given interval. You can pass a CRON string or an interval (or both at once)
  ```bash
  php ./app.php schedule:create -i PT10S
  ```
- `schedule:describe` - displays information about the Schedule as well as a table with data on the last ten executions.  
  ```bash
  php ./app.php schedule:describe
  ```
- `schedule:delete` - deletes the Schedule.
  ```bash
  php ./app.php schedule:delete
  ```
- `schedule:list` - displays a list of all Schedules.
  ```bash
  php ./app.php schedule:list
  ``

An example of the output of the `schedule:describe` command:

```
Schedule `ScheduleID` of type `Schedule.greet` was created at `2023-12-20T15:59:19+00:00`
The Schedule is active
Next action is scheduled in 8.177s

+-------------------------+--------+----------- Recent Actions ---------------+------------------------------+
| Schedule Time           | Delay  | Workflow ID                              | Result                       |
+-------------------------+--------+------------------------------------------+------------------------------+
| 2023.12.20 16:16:55.957 | 0.347s | ScheduledWorkflowID-2023-12-20T16:16:50Z | Wie geht es Ihnen, John Doe? |
| 2023.12.20 16:17:04.325 | 0.072s | ScheduledWorkflowID-2023-12-20T16:17:00Z | Grüße, John Doe!             |
| 2023.12.20 16:17:15.860 | 0.703s | ScheduledWorkflowID-2023-12-20T16:17:10Z | Wie geht es Ihnen, John Doe? |
| 2023.12.20 16:17:23.270 | 0.162s | ScheduledWorkflowID-2023-12-20T16:17:20Z | Hi, John Doe!                |
| 2023.12.20 16:17:39.153 | 0.072s | ScheduledWorkflowID-2023-12-20T16:17:30Z | Hallo, John Doe!             |
| 2023.12.20 16:17:48.652 | 0.069s | ScheduledWorkflowID-2023-12-20T16:17:40Z | Grüße, John Doe!             |
| 2023.12.20 16:17:53.850 | 0.685s | ScheduledWorkflowID-2023-12-20T16:17:50Z | Grüße, John Doe!             |
| 2023.12.20 16:18:01.578 | 0.073s | ScheduledWorkflowID-2023-12-20T16:18:00Z | Hallo, John Doe!             |
| 2023.12.20 16:18:18.730 | 0.584s | ScheduledWorkflowID-2023-12-20T16:18:10Z | Hi, John Doe!                |
| 2023.12.20 16:18:28.790 | 0.074s | ScheduledWorkflowID-2023-12-20T16:18:20Z | Hallo, John Doe!             |
+-------------------------+--------+------------------------------------------+------------------------------+
```