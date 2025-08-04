FROM php:8.2-apache

COPY . /var/www/html/

# Instalar dependências PHP
RUN apt-get update && apt-get install -y libzip-dev && \
    docker-php-ext-install zip && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Rodar o Composer para instalar as dependências
RUN composer install

EXPOSE 80
