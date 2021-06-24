# Temporal PHP SDK samples

The samples in this repository demonstrate the various capabilities of the [Temporal PHP SDK](https://github.com/temporalio/sdk-php) used in conjunction with the [Temporal Server](https://github.com/temporalio/temporal).

If you want to learn more about the Temporal Server and how it works, read the documentation at [https://docs.temporal.io](https://docs.temporal.io).

## About this README
This README provides two different approaches to setup the examples. First is relying on running PHP application in docker engine and
does not require any extra work to start examples. The second approach will require to have PHP installed with GRPC extension but makes possible
to start the application on host machine.

## Docker Compose setup
**1. Download the repository.**
```bash
$ git clone git@github.com:temporalio/samples-php.git
$ cd samples-php
```

**2. Build docker images.**
```bash
$ docker-compose build
```

**3. Start server and application containers.**
```bash
$ docker-compose up
```

**4. Run a sample**

To run a sample in docker use:

```bash
$ docker-compose exec app php app.php {sample-name}
```

To observe active workers:

```bash
$ docker-compose exec app rr workers -i
```

## Local Setup
**1. Make sure you have PHP 7.4, or higher, installed.**

**2. Clone this repo and change directory into the root of the project.**

```bash
$ git clone https://github.com/temporalio/samples-php
$ cd samples-php
```

**3. Install the gRPC PHP extension**

The PHP gRPC engine extension must be installed and activated in order to communicate with the Temporal Server.

Follow the instructions here: [https://cloud.google.com/php/grpc](https://cloud.google.com/php/grpc)

Note: For Windows machines, you can download the `php_grpc.dll` from the [PECL website](https://pecl.php.net/package/gRPC)

Make sure you follow the all the steps to activate the gRPC extension in your  `php.ini` file and install the protobuf runtime library in your project.

**4. Install additional PHP dependencies**

```bash
$ cd app
$ composer install
```

**5. Download RoadRunner application server**

The Temporal PHP SDK requires the RoadRunner 2.0 application server and supervisor to run Activities and Workflows in a scalable way.

```bash
$ cd app
$ ./vendor/bin/rr get
```

Note: You can install RoadRunner manually by downloading its binary from the [release page](https://github.com/spiral/roadrunner/releases/tag/v1.9.2).

**6. Run the Temporal Server**

The Temporal Server must be up and running for the samples to work.
The fastest way to do that is by following the [Quick install guide](https://docs.temporal.io/docs/server/quick-install).

You can also run the included `docker-compose.yml` file. Make sure to comment `app` section.

**7. Update configuration**
Make sure to update the temporal address in `app/.rr.yaml` to `localhost:7233`.  

**8. Start the application using RoadRunner**

By default, all samples run using a single RoadRunner Server instance.
To start the application using RoadRunner:

```bash
$ cd app
$ ./rr serve
```

You can now interact with the samples.

Note: You can alter number of PHP Workers in `app/.rr.yaml`.

**9. Run a sample**

## Samples

Each sample has specific requirements.
Follow the instructions in the README of the sample you wish to run.

<!-- @@@SNIPSTART samples-php-readme-samples-directory -->

### Beginner samples

The following samples demonstrate much of the basic functionality and capabilities of the SDK.

- **[SimpleActivity](https://github.com/temporalio/samples-php/tree/master/app/src/SimpleActivity)**: Single Activity Workflow

- **[ActivityRetry](https://github.com/temporalio/samples-php/blob/master/app/src/ActivityRetry)**: How to retry an Activity

- **[AsyncActivity](https://github.com/temporalio/samples-php/blob/master/app/src/AsyncActivity)**: How to call Activities asynchronously and wait for them using Promises

- **[AsyncActivityCompletion](https://github.com/temporalio/samples-php/tree/master/app/src/AsyncActivityCompletion)**: An asynchronous Activity implementation

- **[AsyncClosure](https://github.com/temporalio/samples-php/blob/master/app/src/AsyncClosure)**: How to run part of a Workflow asynchronously as a separate Task (coroutine)

- **[CancellationScope](https://github.com/temporalio/samples-php/blob/master/app/src/CancellationScope)**: How to explicitly cancel parts of a Workflow

- **[Child](https://github.com/temporalio/samples-php/blob/master/app/src/Child)**: Example of a child Workflow

- **[Cron](https://github.com/temporalio/samples-php/blob/master/app/src/Cron)**: A Workflow that is executed according to a cron schedule

- **[Periodic](https://github.com/temporalio/samples-php/tree/master/app/src/Periodic)**: A Workflow that executes some logic periodically

- **[Exception](https://github.com/temporalio/samples-php/tree/master/app/src/Exception)**: Example of exception propagation and wrapping

- **[PolymorphicActivity](https://github.com/temporalio/samples-php/tree/master/app/src/PolymorphicActivity)**: Activities that extend a common interface

- **[Query](https://github.com/temporalio/samples-php/tree/master/app/src/Query)**: Demonstrates how to Query the state of a single Workflow

- **[Signal](https://github.com/temporalio/samples-php/tree/master/app/src/Signal)**: Example of sending and handling a Signal

- **[Saga](https://github.com/temporalio/samples-php/tree/master/app/src/Saga)**: Example of SAGA pattern support

- **[SearchAttributes](https://github.com/temporalio/samples-php/tree/master/app/src/SearchAttributes)**: Example of Custom search attributes that can be used to find Workflows using predicates

### Advanced samples

The following samples demonstrate some of the more complex aspects associated with running Workflows with the SDK.

- **[FileProcessing](https://github.com/temporalio/samples-php/tree/master/app/src/FileProcessing)**: Demonstrates Task routing features.

- **[Booking SAGA](https://github.com/temporalio/samples-php/tree/master/app/src/BookingSaga)**: Demonstrates Temporal approach to a trip booking SAGA.

- **[Money Transfer](https://github.com/temporalio/samples-php/tree/master/app/src/MoneyTransfer)**: Basic money transfer example.

- **[MoneyBatch](https://github.com/temporalio/samples-php/tree/master/app/src/MoneyBatch)**: Demonstrates a situation when a single deposit should be initiated for multiple withdrawals.

- **[Updatable Timer](https://github.com/temporalio/samples-php/tree/master/app/src/UpdatableTimer)**: Demonstrates the use of a helper class which relies on Workflow.await to implement a blocking sleep that can be updated at any moment.

- **[Subscription](https://github.com/temporalio/samples-php/tree/master/app/src/Subscription)**: Demonstrates a long-running process associated with a user ID. The process charges the user once every 30 days after a one month free trial period.

<!-- @@@SNIPEND -->
