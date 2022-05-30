FROM php:8.1-fpm
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql