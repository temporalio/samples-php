# PHP Temporal Samples

These samples demonstrate various capabilities of PHP Temporal client and server. You can learn more about Temporal at:
* [temporal.io](https://temporal.io)
* [Temporal Service](https://github.com/temporalio/temporal)
* [Temporal Java SDK](https://github.com/temporalio/sdk-java)
* [Temporal Go SDK](https://github.com/temporalio/sdk-go)
* [Temporal PHP SDK](https://github.com/temporalio/sdk-php)

## Setup

Make sure to install PHP version 7.4 and higher. All PHP dependencies are managed via [Composer](https://getcomposer.org/).

### Get the Samples

Run the following commands:

     git clone https://github.com/temporalio/samples-php
     cd samples-php

### Install GRPC PHP Extension

You must install GRPC engine extension in order to communicate with Temporal server.

*Linux:*

Follow the steps here - https://cloud.google.com/php/grpc

*Windows:*

You can download php_grpc.dll from [PECL website](https://pecl.php.net/package/gRPC).

> Make sure to activate extension in php.ini. Install `protobuf` extension for higher performance and production usage.

### Install Dependencies

To install PHP dependencies:

```bash
$ cd app
$ composer install
```

### Download RoadRunner application server

Temporal PHP SDK requires RoadRunner 2.0 application server and supervisor to run activities and workflows in a scalable way.

```bash
$ cd app
$ ./vendor/bin/rr get
```

> You can install RoadRunner manually, by downloading its binary from [release page](https://github.com/spiral/roadrunner/releases/tag/v1.9.2).

### Run Temporal Server

Samples require Temporal service to run. We recommend a locally running version of Temporal Server
managed through [Docker Compose](https://docs.docker.com/compose/gettingstarted/):

     docker-compose up

> If this does not work, see the instructions for running Temporal Server at https://github.com/temporalio/temporal/blob/master/README.md.

## See Temporal UI

The Temporal Server running in a docker container includes a Web UI.

Connect to [http://localhost:8088](http://localhost:8088).

Click on a *RUN ID* of a workflow to see more details about it. Try different view formats to get a different level
of details about the execution history.


## Install Temporal CLI (tctl) - Optional

[Command Line Interface Documentation](https://docs.temporal.io/docs/tctl)

### Start PHP Application

By default, all samples run using single server instance. To start application using RoadRunner:

```bash
$ cd app
$ ./rr serve
```

You can now interact with examples.

> You can alter number of PHP workers in `app/.rr.yaml`.

## Samples

Each sample has specific requirements for running it. 

### HelloWorld

A number of samples dedicated to demonstrate basic capabilities of SDK:

* **[SimpleActivity](https://github.com/temporalio/samples-php/tree/master/app/src/SimpleActivity)**: a single activity workflow
* **[ActivityRetry](https://github.com/temporalio/samples-php/blob/master/app/src/ActivityRetry)**: how to retry an activity
* **[AsyncActivity](https://github.com/temporalio/samples-php/blob/master/app/src/AsyncActivity)**: how to call activities asynchronously and wait for them using Promises
* **[AsyncActivityCompletion](https://github.com/temporalio/samples-php/tree/master/app/src/AsyncActivityCompletion)**: an asynchronous activity implementation
* **[AsyncClosure](https://github.com/temporalio/samples-php/blob/master/app/src/AsyncClosure)**: how to run part of a workflow asynchronously in a separate task (coroutine)
* **[CancellationScope](https://github.com/temporalio/samples-php/blob/master/app/src/CancellationScope)**: how to explicitly cancel parts of a workflow
* **[Child](https://github.com/temporalio/samples-php/blob/master/app/src/Child)**: a child workflow
* **[Cron](https://github.com/temporalio/samples-php/blob/master/app/src/Cron)**: a workflow that is executed according to a cron schedule
* **[Periodic](https://github.com/temporalio/samples-php/tree/master/app/src/Periodic)**: a workflow that executes some logic periodically
* **[Exception](https://github.com/temporalio/samples-php/tree/master/app/src/Exception)**: exception propagation and wrapping
* **[PolymorphicActivity](https://github.com/temporalio/samples-php/tree/master/app/src/PolymorphicActivity)**: activities that extend a common interface
* **[Query](https://github.com/temporalio/samples-php/tree/master/app/src/Query)**: demonstrates how to query a state of a single workflow
* **[Signal](https://github.com/temporalio/samples-php/tree/master/app/src/Signal)**: sending and handling a signal
* **[Saga](https://github.com/temporalio/samples-php/tree/master/app/src/Saga)**: SAGA pattern support
* **[SearchAttributes](https://github.com/temporalio/samples-php/tree/master/app/src/SearchAttributes)**: Custom search attributes that can be used to find workflows using predicates

To run the hello world samples:

```bash
$ php ./app/app.php activity-retry
$ php ./app/app.php async-activity
$ php ./app/app.php async-closure
$ php ./app/app.php cancellation-scope
$ php ./app/app.php child
$ php ./app/app.php cron
$ php ./app/app.php exception
$ php ./app/app.php polymorphic
$ php ./app/app.php query
$ php ./app/app.php saga
$ php ./app/app.php search-attributes
$ php ./app/app.php signal
$ php ./app/app.php simple-activity
``` 

Some examples has multiple endpoints:

```bash
$ php ./app/app.php periodic:start
$ php ./app/app.php periodic:stop
```

## Specialized Samples

We created a number of more complex samples to demonstrate various decision making of workflows.

### File Processing
[FileProcessing](https://github.com/temporalio/samples-php/tree/master/app/src/FileProcessing)
Demonstrates task routing features. The sample workflow downloads a file, processes it, and uploads the result to a destination. 
Any worker can pick up the first activity. However, the second and third activity must be executed on the same host as the first one.

The example registers secondary (host specific) task queue in a `worker.php` file for simplificy:

```php
// We can use task queue for more complex task routing, for example our FileProcessing
// activity will receive unique, host specific, TaskQueue which can be used to process
// files locally.
$hostTaskQueue = gethostname();

$factory->newWorker($hostTaskQueue)
    ->registerActivityImplementations(new FileProcessing\StoreActivity($hostTaskQueue));
```

The `php ./app/app.php process-file {url}` command starts workflows. Each invocation starts a new workflow execution.

    php ./app/app.php process-file https://github.com/temporalio/temporal/archive/v1.6.3.zip

### Booking SAGA

[Booking SAGA](https://github.com/temporalio/samples-php/tree/master/app/src/BookingSaga)
is a Temporal take on Camunda BPMN trip booking example.

To run:

    php ./app/app.php booking-saga

### Money Transfer

Basic [Money Transfer](https://github.com/temporalio/samples-php/tree/master/app/src/MoneyTransfer) example.

    php ./app/app.php money-transfer

### Money Batch

[The sample](https://github.com/temporalio/samples-php/tree/master/app/src/MoneyBatch)
demonstrates a situation when a single deposit should be initiated for multiple withdrawals.
For example, a seller might want to be paid once per fixed number of transactions.
The sample can be easily extended to perform a payment based on more complex criteria like a specific time
or accumulated amount.

    php ./app/app.php money-batch

### Updatable Timer

The [Updatable Timer](https://github.com/temporalio/samples-php/tree/master/app/src/UpdatableTimer) sample
demonstrates a helper class which relies on Workflow.await to implement a blocking sleep that can be updated at any moment.

Money Batch example has two separate commands to interact with server. One to start workflow execution, 
and the second one to send signals to request timer updates.

Start workflow execution:

    php ./app/app.php updatable-timer:start

Extend timer duration:

    php ./app/app.php updatable-timer:prolong

### Subscription

[The sample](https://github.com/temporalio/samples-php/tree/master/app/src/Subscription) demonstrates a long-running
process associated with a user ID. The process will charge user once every 30 days with free trial period for the first months.

Start workflow execution:

    php ./app/app.php subscribe:start {name}

Cancel subscription:

    php ./app/app.php subscribe:cancel {name}
