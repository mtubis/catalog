#!/usr/bin/env bash
set -euo pipefail


cd /var/www/html


# 1) Composer deps (if bind-mount hides vendor from image)
if [ ! -f vendor/autoload.php ]; then
echo "[entrypoint] Installing composer dependencies..."
composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
fi


# 2) Link to storage (idempotent)
php artisan storage:link || true


# 3) Caches for production
php artisan config:cache --no-interaction || true
php artisan route:cache --no-interaction || true
php artisan view:cache --no-interaction || true


# 4) Data migrations/imports (ENV driven)
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
echo "[entrypoint] Running migrations..."
php artisan migrate --force --no-interaction || true
fi


if [ -n "${IMPORT_DIR:-}" ]; then
echo "[entrypoint] Importing catalog from ${IMPORT_DIR}..."
php artisan catalog:import --dir="${IMPORT_DIR}" || true
fi


# 5) Odpal proces docelowy (php-fpm w foreground)
exec "$@"
