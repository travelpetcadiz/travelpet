# syntax=docker/dockerfile:1

# --- 1. Dependencias PHP ---------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# --- 2. Assets (Tailwind + Livewire + MapLibre) -----------------------------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
# app.js importa Livewire desde vendor
COPY --from=vendor /app/vendor/livewire ./vendor/livewire
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources ./vendor/laravel/framework/src/Illuminate/Pagination/resources
RUN npm run build

# --- 3. Imagen final: FrankenPHP (Caddy + PHP en un solo proceso) -----------
FROM dunglas/frankenphp:1-php8.3-bookworm

RUN install-php-extensions pdo_mysql intl zip bcmath opcache pcntl \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini $PHP_INI_DIR/conf.d/zz-travelpet.ini

WORKDIR /app
COPY --from=vendor /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 80
ENTRYPOINT ["entrypoint"]
CMD ["frankenphp", "php-server", "--root", "public", "--listen", ":80"]
