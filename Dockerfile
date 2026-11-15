FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libzip-dev libicu-dev \
    libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd intl zip \
    && docker-php-ext-enable intl zip \
    && a2enmod rewrite \
    && a2dismod mpm_event \
    && a2enmod mpm_prefork \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurar Apache para escuchar en el puerto 8080 (Railway)
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:8080>/' /etc/apache2/sites-enabled/000-default.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY . .

RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 8080

CMD apache2-foreground
