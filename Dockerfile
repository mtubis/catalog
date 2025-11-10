FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx bash curl icu-dev oniguruma-dev libzip-dev libpq-dev tzdata supervisor \
 && docker-php-ext-install -j$(nproc) intl bcmath pdo pdo_pgsql zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# produkcyjny vendor i prawa
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
 && php artisan storage:link || true \
 && chown -R www-data:www-data /var/www/html \
 && find /var/www/html -type f -exec chmod 644 {} \; \
 && find /var/www/html -type d -exec chmod 755 {} \; \
 && chmod -R 775 storage bootstrap/cache

# Nginx + supervisord + entrypoint
COPY docker/nginx/default.conf.template /etc/nginx/http.d/default.conf.template
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENV PORT=8080
EXPOSE 8080
ENTRYPOINT ["/entrypoint.sh"]
CMD ["supervisord","-c","/etc/supervisord.conf"]
