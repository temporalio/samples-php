# Interceptors sample

This sample demonstrates how you can use Interceptors.

There are few interceptors:
- RequestLoggerInterceptor - logs all internal requests from the Workflow into the RoadRunner log.
- ActivityAttributesInterceptor - reads [ActivityOption](./Attribute/ActivityOption.php) attributes and applies them to the Activity options.

```bash
php ./app/app.php interceptors
```
