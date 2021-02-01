# Temporal PHP SDK Samples
This repository contains examples of PHP driven workflows, activities. Client interactions are available in a form of
Symfony/Console commands.

## Local Installation
You can run samples locally without starting application docker container. However, you will need to install needed
PHP dependencies and start local temporal server.

### Dependencies
Make sure to install PHP version 7.4 and higher. 

**GRPC:**

A GRPC extension is required to communicate with Temporal server.

*Linux:*
Follow the steps here - https://cloud.google.com/php/grpc

*Windows:*
You can download php_grpc.dll from [PECL website](https://pecl.php.net/package/gRPC).

> Install `protobuf` extension for higher performance. 

**Packages:**

Install all required composer packages:

```bash
$ cd app
$ composer install
```

**RoadRunner:**

Temporal PHP SDK requires RoadRunner 2.0 application server and supervisor to run activities and workflows in scalable way.
You can install RoadRunner by downloading its binary from [release page](https://github.com/spiral/roadrunner/releases/tag/v1.9.2).

Or run local command to download server automatically:

```bash
$ cd app
$ ./vendor/bin/rr get
```

### Start Application
To start Temporal server locally:

```bash
$ docker-compose -f .\docker-compose.yml up
```

To start application using application server:

```bash
$ cd app
$ ./rr serve
```

You can now interact with examples.

### Running Samples
To view list of available commands:

```bash
$ php ./app/app.php
```

To run sample workflow:

```bash
$ php ./app/app.php simple-activity
```