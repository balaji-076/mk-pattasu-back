FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libzip-dev \
    oniguruma-dev \
    curl \
    unzip

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    opcache

RUN sed -i 's|listen = .*|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --optimize-autoloader \
    --no-interaction

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

RUN php artisan package:discover --ansi \
 && php artisan route:cache \
 && php artisan view:cache

RUN mkdir -p /var/log/supervisor

COPY nginx.conf /etc/nginx/http.d/default.conf
COPY supervisor.conf /etc/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]