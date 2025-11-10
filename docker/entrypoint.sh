#!/usr/bin/env sh
set -e

cd /var/www/html

# cache + (opcjonalnie) migracje / import
php artisan config:cache || true
php artisan route:cache  || true
php artisan view:cache   || true
[ "${RUN_MIGRATIONS:-false}" = "true" ] && php artisan migrate --force || true
[ -n "${IMPORT_DIR:-}" ] && php artisan catalog:import --dir="${IMPORT_DIR}" || true

# wstrzyknięcie PORT do nginx
envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

exec "$@"
