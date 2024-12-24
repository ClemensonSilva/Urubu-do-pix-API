FROM php:fpm 
#instalando pdo
RUN docker-php-ext-install pdo pdo_mysql
#instalando xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apt-get update && apt-get install -y \ 
    git \
    unzip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . /app