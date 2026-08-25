# Development image for light-service-php.
#
# Everything (PHP, Composer, PHPUnit) lives in here so the library can be
# developed and tested without installing a PHP toolchain on the host.
#
# Override the runtime to test against another supported version, e.g.
#   docker build --build-arg PHP_VERSION=8.2 -t light-service-php:8.2 .
ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-cli-alpine

# git + unzip let Composer install from dist archives and VCS sources.
RUN apk add --no-cache git unzip

# pcov is the code coverage driver, matching what CI installs.
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
 && pecl install pcov \
 && docker-php-ext-enable pcov \
 && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

WORKDIR /app

# Resolve dependencies first so the layer is cached across source edits.
# There is no committed composer.lock (this is a library), so dependencies
# resolve against whichever PHP version this image provides.
COPY composer.json ./
RUN composer update --prefer-dist --no-progress

COPY . .

CMD ["composer", "test"]
