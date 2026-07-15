# Production image: Laravel + Filament served by Apache on :80
# (infra/nginx proxies /admin and /api here).

FROM composer:latest AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-req=ext-intl
COPY . .
RUN composer dump-autoload --optimize --no-dev

FROM php:8.4-apache
RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libpq-dev \
    && docker-php-ext-install intl pdo_pgsql opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html
COPY --from=vendor /app ./
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# On every container start: wipe ALL Laravel caches (config/routes/views/events/app cache),
# then rebuild them for production before serving. storage:link exposes admin uploads
# (storage/app/public, persisted via compose volume) at public/storage.
CMD php artisan optimize:clear \
    && php artisan optimize \
    && php artisan filament:optimize \
    && php artisan storage:link --force \
    && apache2-foreground
