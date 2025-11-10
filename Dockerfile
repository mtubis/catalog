# ---- base runtime ----
FROM php:8.3-fpm-alpine AS base


# System deps
RUN apk add --no-cache \
bash git curl unzip icu-dev oniguruma-dev libzip-dev libpq-dev \
tzdata


# PHP extensions
RUN docker-php-ext-install -j$(nproc) \
intl bcmath pdo pdo_pgsql zip opcache


# Redis
RUN pecl install redis \
&& docker-php-ext-enable redis


# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# PHP/OPcache
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini


WORKDIR /var/www/html


# Code copy
COPY . /var/www/html


# Permissions for storage/bootstrap
RUN mkdir -p storage/bootstrap/cache \
&& chown -R www-data:www-data storage bootstrap/cache \
&& chmod -R 775 storage bootstrap/cache


# Entrypoint – will do composer install, cache, migrations (conditionally)
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh


USER www-data


EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm", "-F"]
