FROM php:7.4-cli-alpine

COPY --from=mlocati/php-extension-installer:1.2.25 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
      pcov

COPY --from=composer:2.5.1 /usr/bin/composer /usr/local/bin/

COPY ../ /app

WORKDIR /app
