FROM php:7.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
  nano \
  bash \
  libzip-dev \
  unzip \
  libonig-dev

# Install PHP Extensions
RUN docker-php-ext-install zip \
  && docker-php-ext-install opcache sockets mbstring \
  && docker-php-ext-enable opcache sockets mbstring

# Protobuf and GRPC
ENV PROTOBUF_VERISON "3.14.0"
RUN pecl channel-update pecl.php.net
RUN pecl install protobuf-${PROTOBUF_VERISON} grpc \
    && docker-php-ext-enable protobuf grpc

# Install Temporal CLI
COPY --from=temporalio/admin-tools /usr/local/bin/tctl /usr/local/bin/tctl

# Install Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Wait for it
COPY wait-for-temporal.sh /usr/local/bin
RUN chmod +x /usr/local/bin/wait-for-temporal.sh

# Application codebase
WORKDIR /var/app
COPY app/ /var/app

RUN composer install

# Setup RoadRunner
RUN vendor/bin/rr get && chmod +x rr && mv rr /usr/local/bin/rr