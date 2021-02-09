# AsyncActivityCompletion sample

This sample demonstrates an asynchronous Activity implementation

From the root of the project, run the following command to start the Workflow:

```bash
php ./app/app.php user-activity:start
```

To complete activity execution:

```bash
php ./app/app.php user-activity:complete {TOKEN} {MESSAGE}
```

> Token can be found in application log. 