IMAGE     ?= temporal-php-lambda:latest
# The binary is built by velox from the plugin list in velox.toml.
# Install it with: go install github.com/roadrunner-server/velox/v3/cmd/vx@latest
VX        ?= vx
CONTAINER ?= temporal-php-lambda
PORT      ?= 9000
PLATFORM  ?= linux/arm64
ARCH      ?= $(shell uname -m | sed 's/aarch64/arm64/;s/arm64/arm64/')
RIE_URL    = https://github.com/aws/aws-lambda-runtime-interface-emulator/releases/download/v1.29/aws-lambda-rie-$(ARCH)

TEMPORAL_ADDRESS         ?= host.docker.internal:7233
TEMPORAL_NAMESPACE       ?= default
TEMPORAL_TASK_QUEUE      ?= php-lambda
TEMPORAL_DEPLOYMENT_NAME ?=
TEMPORAL_BUILD_ID        ?= v1
LAMBDA_TIMEOUT           ?= 30

.PHONY: rr build run invoke logs stop

rr:
	GITHUB_TOKEN=$${GITHUB_TOKEN:-$$(gh auth token)} $(VX) build -c velox.toml -o .
	@echo "built lambda/rr through velox"

build: rr
	docker build --platform $(PLATFORM) -f Dockerfile -t $(IMAGE) ..

.rie/aws-lambda-rie:
	@mkdir -p .rie
	curl -fsSL -o $@.tmp $(RIE_URL)
	chmod +x $@.tmp && mv $@.tmp $@

run: build .rie/aws-lambda-rie
	-docker rm -f $(CONTAINER) 2>/dev/null
	docker run -d --name $(CONTAINER) -p $(PORT):8080 \
		--entrypoint /rie/aws-lambda-rie \
		-v "$(CURDIR)/.rie":/rie:ro \
		-e AWS_LAMBDA_FUNCTION_TIMEOUT=$(LAMBDA_TIMEOUT) \
		-e TEMPORAL_ADDRESS=$(TEMPORAL_ADDRESS) \
		-e TEMPORAL_NAMESPACE=$(TEMPORAL_NAMESPACE) \
		-e TEMPORAL_TASK_QUEUE=$(TEMPORAL_TASK_QUEUE) \
		-e TEMPORAL_DEPLOYMENT_NAME=$(TEMPORAL_DEPLOYMENT_NAME) \
		-e TEMPORAL_BUILD_ID=$(TEMPORAL_BUILD_ID) \
		$(IMAGE) rr serve
	@echo "listening on http://localhost:$(PORT)"

invoke:
	@curl -sS -XPOST "http://localhost:$(PORT)/2015-03-31/functions/function/invocations" \
		-d '{}' -w '\ninvocation took %{time_total}s\n'

logs:
	docker logs -f $(CONTAINER)

stop:
	-docker rm -f $(CONTAINER)
