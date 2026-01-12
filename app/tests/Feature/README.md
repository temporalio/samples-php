# Workflow testing sample

There is an implementation of [the testing guide](https://github.com/temporalio/sdk-php/tree/master/testing).

To load all the required binaries outside the docker container, run the following command:

```bash
composer load:binaries
```

To run the tests, use the following command:

```bash
composer test:feat
```
