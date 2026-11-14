ARG PHP_VERSION=8.3-cli-alpine
ARG COMPOSER_VERSION=2.9.4
ARG PROTOBUF_VERSION=5.36.0

FROM composer:${COMPOSER_VERSION} AS composer


FROM php:${PHP_VERSION} AS extensions
ARG PROTOBUF_VERSION
RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers; \
    pecl install protobuf-${PROTOBUF_VERSION}; \
    docker-php-ext-enable protobuf; \
    docker-php-ext-install -j"$(nproc)" pcntl sockets; \
    apk del .build-deps

FROM extensions AS vendor
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/task
COPY lambda/composer.json lambda/composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --no-progress --prefer-dist
COPY app/src/Lambda/ ./src/
RUN composer dump-autoload --no-dev --classmap-authoritative

FROM php:${PHP_VERSION} AS runtime

COPY --from=extensions /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=extensions /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY lambda/rr /usr/local/bin/rr
COPY lambda/php.ini /usr/local/etc/php/conf.d/zz-lambda.ini

# Lambda gives a writable /tmp only; keep every cache out of the read-only image.
ENV HOME=/tmp \
    LAMBDA_TASK_ROOT=/var/task

WORKDIR /var/task
COPY --from=vendor /var/task/vendor/ ./vendor/
COPY app/src/Lambda/ ./src/
COPY lambda/worker.php lambda/.rr.yaml ./

RUN addgroup -g 10001 -S app && adduser -S -D -H -u 10001 -G app -h /tmp app
USER 10001:10001

ENTRYPOINT ["rr", "serve"]
