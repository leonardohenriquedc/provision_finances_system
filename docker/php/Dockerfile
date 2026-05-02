FROM php:8.4-fpm 

RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libonig-dev \ 
    && docker-php-ext-install pdo pdo_mysql mbstring zip

RUN pecl install redis && docker-php-ext-enable redis 

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer 

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT [ "entrypoint.sh" ]

CMD ["php-fpm"]