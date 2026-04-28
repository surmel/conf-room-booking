FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    sqlite-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_sqlite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

COPY . .

RUN composer dump-autoload --optimize --no-interaction

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
