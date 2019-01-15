FROM php:fpm

RUN apt-get update && apt install git -y \
    curl \
    sed \
    zip \
    unzip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY composer.json /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN docker-php-ext-install pdo_mysql
RUN composer install && composer update \
	composer dumpautoload

WORKDIR /var/www