ARG TEMPORAL_CLI_IMAGE=temporalio/temporal:1.10.0@sha256:003508da05989e7cf0d583cfce521c4bb2732e733574e3040db8f1284783baa2
FROM ${TEMPORAL_CLI_IMAGE} AS temporal-cli

FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
  nano \
  bash \
  libzip-dev \
  unzip \
  libonig-dev

# Install PHP Extensions
ENV CFLAGS="$CFLAGS -D_GNU_SOURCE"
RUN docker-php-ext-install zip \
  && docker-php-ext-install opcache sockets mbstring \
  && docker-php-ext-enable opcache sockets mbstring

# Protobuf and GRPC
ENV PROTOBUF_VERSION="3.25.1"
ARG PHP_EXTENSION_JOBS=2
RUN pecl channel-update pecl.php.net
RUN MAKEFLAGS="-j${PHP_EXTENSION_JOBS}" pecl install protobuf-${PROTOBUF_VERSION} grpc \
    && docker-php-ext-enable protobuf grpc

# Install Temporal CLI
COPY --from=temporal-cli /usr/local/bin/temporal /usr/local/bin/temporal

# Install Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Wait for Temporal service to start up
COPY wait-for-temporal.sh /usr/local/bin
RUN chmod +x /usr/local/bin/wait-for-temporal.sh

# Copy application codebase
WORKDIR /var/app
COPY app/ /var/app

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction

# Load binaries
RUN vendor/bin/dload get --no-interaction
