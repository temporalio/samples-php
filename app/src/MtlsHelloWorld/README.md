# Hello World with mTLS

This example shows how to secure your Temporal application with mTLS.
This is required to connect with Temporal Cloud or any production Temporal deployment.

### Running this sample

1. Start Road Runner with mtls config:
```bash
 ./rr serve -c app/src/MtlsHelloWorld/.rr.yaml
```

2. Run application code:
```bash
php app/src/MtlsHelloWorld/app.php
```


