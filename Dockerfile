FROM php:7.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
  nano \
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

# Install Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Download RoadRunner
ENV RR_VERSION "2.0.0-beta9"
RUN mkdir /tmp/rr \
  && cd /tmp/rr \
  && echo "{\"require\":{\"spiral/roadrunner\":\"${RR_VERSION}\"}}" >> composer.json \
  && composer install \
  && vendor/bin/rr get-binary -l /usr/local/bin \
  && rm -rf /tmp/rr

WORKDIR /var/app

ENTRYPOINT ["/usr/local/bin/rr", "serve", "-c", "/var/app/.rr.yaml"]