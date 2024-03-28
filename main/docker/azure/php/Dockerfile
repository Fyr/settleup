FROM node:18-alpine as node

FROM php:8.1-fpm-alpine as build-stage

COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/share /usr/local/share
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin

# set up system components
RUN apk update && apk add --no-cache \
    curl \
    git \
    wget \
    unzip \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libmcrypt-dev \
    libbz2 \
    bzip2-dev \
    gettext-dev \
    linux-headers \
    g++ \
    make \
    autoconf

# # set up php plugins
RUN docker-php-ext-install mysqli pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gettext \
    && docker-php-ext-install bz2 intl gd gettext zip

RUN pecl list | grep mcrypt || (pecl install mcrypt && docker-php-ext-enable mcrypt)
RUN pecl list | grep xdebug || (pecl install xdebug && docker-php-ext-enable xdebug)

# PHP customizations
COPY ./docker/php/conf.d/limits.ini /usr/local/etc/php/conf.d/limits.ini

# set up composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Copy data
COPY . /var/www/html

# change working directory
WORKDIR /var/www/html

# rebuild Composer packages
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install

# install frontend packages
RUN npm i

# rebuild Frontend assets
RUN npm run build

RUN mv application.ini application/configs/application.ini

# update permissions
RUN chmod -R 0777 ./data


# ######
FROM php:8.1-fpm-alpine
RUN apk update && apk add --no-cache \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libmcrypt-dev \
    libbz2 \
    bzip2-dev \
    gettext-dev \
    nginx

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY --from=build-stage /usr/local/lib/php /usr/local/lib/php
COPY --from=build-stage /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=build-stage /var/www/html /var/www/html

COPY ./docker/azure/php/default.conf /etc/nginx/http.d/default.conf
COPY ./docker/azure/php/sshd_config /etc/ssh/
COPY ./docker/azure/php/entrypoint.sh /entrypoint.sh
# Start and enable SSH
RUN apk add openssh \
    && echo "root:Docker!" | chpasswd \
    && chmod +x /entrypoint.sh \
    && cd /etc/ssh/ \
    && ssh-keygen -A

EXPOSE 80 443 2222

CMD ["/entrypoint.sh"]
