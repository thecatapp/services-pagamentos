#!/bin/sh

composer require vladimir-yuldashev/laravel-queue-rabbitmq --ignore-platform-req=ext-sockets && php artisan serve --host 0.0.0.0 --port 8000
