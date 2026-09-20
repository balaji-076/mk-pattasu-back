FROM php:8.4-cli

ARG UID=1000
ARG GID=1000
ARG NODE_VERSION=22.0.0

RUN apt-get update && apt-get install -y --no-install-recommends \
      curl unzip libssl-dev libzip-dev \
      && docker-php-ext-install pdo_mysql zip \
      && pecl install redis xdebug \
      && docker-php-ext-enable redis xdebug \
      && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

USER www
WORKDIR /var/www
CMD ["bash"]
