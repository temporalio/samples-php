# InfrequentPolling sample

This sample demonstrates infrequent polling by using Activity retries.

The Workflow calls an Activity that polls a test service. The test service fails the first four
poll attempts, then succeeds on the fifth attempt. The Activity failure is marked as a benign
`ApplicationFailure`, which indicates that the failure is expected and should have lower
observability severity.

The retry policy uses a fixed interval by setting:

- `InitialInterval` to the polling interval, 60 seconds in this sample
- `BackoffCoefficient` to `1.0`

From the root of the project, run the following command:

```bash
php ./app/app.php infrequent-polling
```
