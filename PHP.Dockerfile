FROM php:fpm 
#instalando pdo
RUN docker-php-ext-install pdo pdo_mysql
#instalando xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug