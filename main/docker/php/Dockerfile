FROM php:8.1-fpm

# set up system components
RUN apt-get update && apt-get install -y \
    ca-certificates curl gnupg \
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

# set up nodejs
RUN set -uex; \
    apt-get update; \
    apt-get install -y ca-certificates curl gnupg; \
    mkdir -p /etc/apt/keyrings; \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
     | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg; \
    NODE_MAJOR=18; \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" \
     > /etc/apt/sources.list.d/nodesource.list; \
    apt-get update; \
    apt-get install nodejs -y;


# change working directory
#WORKDIR /var/www/html

# rebuild Composer packages
#ADD ./composer.json /var/www/html
#RUN COMPOSER_ALLOW_SUPERUSER=1 composer install

# install frontend packages
#RUN npm i

# rebuild Frontend assets
#RUN npm run build

# update permissions
COPY ./data ./data
RUN chmod -R 0777 ./data

