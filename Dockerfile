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
    && apt-get install -y --no-install-recommends libicu-dev libpq-dev libzip-dev \
       libpng-dev libjpeg-dev libfreetype-dev libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install intl pdo_pgsql opcache zip gd exif \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html
COPY --from=vendor /app ./
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# This CMD runs as root, so files it creates (compiled views, etc.) end up root-owned —
# chown before serving, or Apache's www-data workers get "touch(): Operation not permitted"
# recompiling any view not covered by the ahead-of-time view:cache (e.g. Livewire relation
# manager views), which Livewire swallows as a silently-blank nested component.
CMD php artisan optimize:clear \
    && php artisan optimize \
    && php artisan filament:optimize \
    && php artisan storage:link --force \
    && chown -R www-data:www-data storage bootstrap/cache \
    && apache2-foreground
