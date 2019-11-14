FROM  php:7.2-fpm

#Install git
RUN apt-get update \
    && apt-get install -y git
RUN docker-php-ext-install pdo pdo_mysql mysqli
EXPOSE 80
