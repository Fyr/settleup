FROM php:8.2-fpm

# set up system components
RUN apt-get update && apt-get install -y \
    git \
    wget \
    unzip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libmcrypt-dev \
    libbz2-dev

# set up php plugins
RUN docker-php-ext-install mysqli pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gettext \
    && docker-php-ext-install bz2 intl gd gettext zip

RUN pecl list | grep mcrypt || (pecl install mcrypt && docker-php-ext-enable mcrypt)
RUN pecl list | grep xdebug || (pecl install xdebug && docker-php-ext-enable xdebug)

# set up composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN chmod 0755 /usr/local/bin/composer

COPY ./storage ./storage

RUN chmod -R 0777 ./storage
