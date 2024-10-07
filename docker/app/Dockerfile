FROM php:8.3-cli-alpine as build

# set memory limit
# RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# copy binaries
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=temporalio/admin-tools /usr/local/bin/tctl /usr/local/bin/tctl
COPY --from=ghcr.io/roadrunner-server/roadrunner:latest /usr/bin/rr /usr/local/bin/rr
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# copy and chmod wait script
COPY wait-for-temporal.sh /usr/local/bin
RUN chmod +x /usr/local/bin/wait-for-temporal.sh

# install deps and php extensions
RUN apk --no-cache add git && \
    install-php-extensions \
    zip \
    mbstring \
    opcache \
    sockets \
    protobuf \
    grpc

FROM build AS vendor

WORKDIR /var/app
COPY app/composer.json .

# build `vendor` with all deps
RUN composer install --prefer-dist --no-ansi --no-dev --no-autoloader --no-scripts

FROM vendor as app

WORKDIR /var/app
COPY app .

# build autoload
RUN composer dump-autoload
