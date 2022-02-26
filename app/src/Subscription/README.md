# Subscription sample

This sample demonstrates a long-running process associated with a user ID.
The process charges the user once every 30 days after a one month free trial period.

**Start subscription**

From the root of the project, run the following command:

```bash
php ./app/app.php subscribe:start {name}
```

**Cancel subscription**

From the root of the project, run the following command:

```bash
php ./app/app.php subscribe:cancel {name}
```

For more details please read our ["Subscription Walkthrough in PHP"](https://docs.temporal.io/docs/php/subscription-tutorial) tutorial.
