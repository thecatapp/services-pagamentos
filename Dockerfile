FROM php:8.2-alpine

WORKDIR /var/www/html

RUN apk update
RUN apk add postgresql-dev
RUN docker-php-ext-install pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | \
            php -- --install-dir=/usr/bin/ --filename=composer

RUN sh -c sh -c composer install
RUN sh -c sh -c composer update --no-scripts

ENTRYPOINT ["sh", "-c", "chmod 777 ./dev/start.sh && sh ./dev/start.sh"]
