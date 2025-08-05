FROM php:8.3-fpm-alpine


RUN apk add --no-cache \
    git \
    zip \
    unzip \
    # Pour PostgreSQL
    libpq-dev 
    # Pour MySQL
    # mysql-dev

RUN docker-php-ext-install \
pdo_mysql \
pdo_pgsql
    


RUN docker-php-ext-enable opcache


WORKDIR /var/www/html


COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./wait-for-db.sh /usr/local/bin/wait-for-db.sh
RUN chmod +x /usr/local/bin/wait-for-db.sh


EXPOSE 9000

# Démarrez PHP-FPM
CMD ["php-fpm"]