FROM php:8.2-alpine

COPY ./dev ./dev
COPY ./SCC_ROOT_CA.crt /usr/local/share/ca-certificates/SCC_ROOT_CA.crt

WORKDIR /var/www/html

RUN apk update
RUN apk add postgresql-dev
RUN docker-php-ext-install pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | \
            php -- --install-dir=/usr/bin/ --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN sh -c composer install
RUN sh -c composer update --no-scripts

ENTRYPOINT ["sh", "-c", "chmod 777 ./dev/start.sh && ./dev/start.sh"]
