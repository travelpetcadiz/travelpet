#!/usr/bin/env bash
set -euo pipefail
cd /app

echo "Esperando a MySQL en ${DB_HOST}:${DB_PORT:-3306} ..."
until php -r 'exit(@fsockopen(getenv("DB_HOST"), (int) (getenv("DB_PORT") ?: 3306)) ? 0 : 1);'; do
    sleep 2
done

php artisan migrate --force
# Primera vez: carga los sitios de Cádiz desde datos.json
php artisan travelpet:importar --si-vacio

# Cachés de producción (se generan al arrancar para usar las variables del contenedor)
php artisan optimize
php artisan filament:optimize
php artisan storage:link 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache

exec "$@"
