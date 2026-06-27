# 1. Базовый образ: стабильный PHP 8.2 со встроенным веб-сервером Apache
FROM php:8.2-apache

# 2. Установка системных библиотек Linux и расширений PHP.
# Сюда входят драйверы для текущей MySQL, будущей PostgreSQL и библиотека GD для картинок.
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mysqli pdo_pgsql pgsql

# 3. Включаем модуль mod_rewrite для Apache
RUN a2enmod rewrite

# 4. Задаем системный корень веб-сервера Apache
WORKDIR /var/www/html

# 5. Копируем весь исходный код внутрь контейнера
COPY . /var/www/html/

# 6. Копируем файл-пример в рабочий файл конфигурации.
RUN cp settings-example.php settings.php

# 7. Декларируем стандартный веб-порт
EXPOSE 80