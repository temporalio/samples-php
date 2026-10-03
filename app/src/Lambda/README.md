# AWS Lambda

A Temporal worker running as an AWS Lambda custom runtime.

Lambda has no place for a long-lived process: a container is alive only while it handles an
invocation. RoadRunner itself handles that, through its `lambda` plugin: the plugin serves the
Lambda Runtime API, and on every invocation it starts the Temporal workers, lets them poll until
the deadline minus a shutdown buffer, then drains and stops them so the runtime can still answer.
The PHP worker pools stay up for the whole lifetime of the execution environment, so no PHP
process is restarted between invocations. Workflow state lives on the server, not in the process,
so a workflow survives across invocations by replay.

The PHP side needs nothing Lambda-specific: `lambda/worker.php` is an ordinary worker built with
`WorkerFactory::create()`.

The sample workflow makes that visible: it runs an activity, then sleeps for 30 seconds. With the
default function timeout of 30 seconds one invocation polls for 23 of them, so it is not enough —
the first invocation runs the activity and leaves the workflow sleeping, the second one picks it
up and returns the result.

## Layout

| path | role |
|---|---|
| `app/src/Lambda/` | workflow, activity and the client command |
| `lambda/worker.php` | the worker the function runs, with Lambda-tuned `WorkerOptions` |
| `lambda/.rr.yaml` | RoadRunner config: the `lambda` section plus the usual `rpc:` and `temporal:` |
| `lambda/Dockerfile` | the image, entrypoint `rr serve` |
| `lambda/velox.toml` | the plugin list the RoadRunner binary is built from |
| `lambda/Makefile` | build the binary and the image, run under the Runtime Interface Emulator, invoke |

## Run it locally

**1. Start Temporal.** From the repository root:

```bash
docker compose up -d temporal
```

**2. Build the image and start the emulator.**

```bash
cd lambda && make run
```

`make run` builds the RoadRunner binary with [velox](https://github.com/roadrunner-server/velox)
from the plugin list in `lambda/velox.toml`, builds the image, downloads the AWS Lambda Runtime
Interface Emulator and starts the function on `http://localhost:9000`. Install velox once with
`go install github.com/roadrunner-server/velox/v3/cmd/vx@latest`. For an x86 function set
`arch = "amd64"` in `velox.toml` and pass `PLATFORM=linux/amd64`.

The binary carries only the plugins the worker needs, which is why it is 28MB rather than the
59MB of the official build. While the `lambda` plugin is unreleased, `velox.toml` points at a
local checkout through a `[[replaces]]` block; drop that block once it ships.

**3. Start the workflow.** In another terminal:

```bash
docker compose exec app php app.php lambda
```

It starts the workflow on the `php-lambda` task queue and waits for the result.

**4. Invoke the function.** Each call runs the worker for one invocation:

```bash
cd lambda && make invoke
```

Call it twice: the first invocation runs the activity, the second one resumes the workflow after
the timer and the client prints

```
Hello, Antony! (resumed after a 30 second timer)
```

`make logs` follows the worker output, `make stop` removes the container.

## Configuration

The plugin takes over only when `AWS_LAMBDA_RUNTIME_API` is present, so the same image behaves
like an ordinary worker outside Lambda. Its two options live in `lambda/.rr.yaml`:

| option | default | meaning |
|---|---|---|
| `lambda.shutdown_buffer` | `graceful_timeout + 1s` | time reserved before the deadline to drain the workers and answer the Runtime API |
| `lambda.graceful_timeout` | `5s` | how long the workers may drain in-flight tasks; also becomes the worker's `WorkerStopTimeout` unless `worker.php` sets one |

The buffer must exceed the graceful timeout, otherwise the plugin refuses to start. Set the Lambda
function timeout well above the buffer, or every invocation ends before the worker has polled
anything.

The worker's own variables — `TEMPORAL_ADDRESS`, `TEMPORAL_NAMESPACE` and `TEMPORAL_TASK_QUEUE` —
are read by `lambda/worker.php` and `lambda/.rr.yaml`.

## Deploy

Push the image to ECR and create the function from it. A container image needs no runtime
identifier — pick the architecture that matches the image instead; `make build` targets
`linux/arm64`, so the function has to be arm64 as well (pass `PLATFORM=linux/amd64 GOARCH=amd64`
for x86).

Nothing triggers the worker on its own: drive it with an EventBridge schedule, and size the
function timeout and the schedule interval to the latency you want from your task queue. The
function bills for the whole invocation, polling included, so the schedule is the knob that
trades cost against how quickly a task is picked up.

Give the function at least 512 MB. One invocation runs RoadRunner plus three PHP processes and
sits around 95 MB on top of the base image, and Lambda scales CPU with memory, so the smallest
sizes also make the start slower.

Reaching Temporal from Lambda is on you: a function with no VPC attached has internet access and
can dial Temporal Cloud directly, while a self-hosted server inside a VPC needs the function
attached to that VPC. Temporal Cloud also needs credentials, which this sample does not set up —
add a `tls` section with the client certificate and key, or an API key, to `lambda/.rr.yaml`.

## Worker deployment versioning

Temporal's Serverless Worker support expects versioned workers, so that a workflow stays on the
build it started on. Set `TEMPORAL_DEPLOYMENT_NAME` and `TEMPORAL_BUILD_ID` and the worker
registers with `VersioningBehavior::Pinned`:

```bash
cd lambda && make run TEMPORAL_DEPLOYMENT_NAME=php-lambda-worker TEMPORAL_BUILD_ID=v1
```

A versioned worker receives no tasks until its version is made current, so do that once per
build id:

```bash
temporal worker deployment set-current-version \
    --deployment-name php-lambda-worker --build-id v1 --yes
```

Leaving `TEMPORAL_DEPLOYMENT_NAME` empty, as the sample does by default, runs the worker
unversioned.
