FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    libonig-dev libxml2-dev libcurl4-openssl-dev unzip git \
    && docker-php-ext-install pdo_mysql mbstring xml curl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs

RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction \
        --no-progress --optimize-autoloader --ignore-platform-reqs

RUN chown -R www-data:www-data storage bootstrap/cache

CMD sh -c 'port="${PORT:-10000}"; sed -i "s/Listen 80/Listen ${port}/" /etc/apache2/ports.conf; sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf; exec apache2-foreground'