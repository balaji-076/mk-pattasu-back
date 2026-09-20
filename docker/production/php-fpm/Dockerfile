FROM php:8.2-fpm-alpine

# System deps
RUN apk add --no-cache \
    nginx \
    libpq \
    libzip \
    oniguruma \
    curl \
    unzip

# PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy only composer files first (cache layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy full app
COPY . .

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Laravel optimizations
RUN php artisan key:generate --force \
 && php artisan config:clear \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

EXPOSE 9000
CMD ["php-fpm"]
