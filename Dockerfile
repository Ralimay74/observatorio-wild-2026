FROM php:8.3-fpm

# Instalar extensiones de PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && php artisan config:clear \
    && php artisan view:clear \
    && chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 8000

# Comando de inicio
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
