FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    git \
    wget \
    unzip \
    nginx \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libmcrypt-dev \
    libbz2-dev \
    nodejs \
    npm

#RUN apt-get install nodejs npm -y

RUN docker-php-ext-install mysqli pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gettext \
    && docker-php-ext-install bz2 intl gd gettext zip

# Start and enable SSH
RUN apt-get update \
    && apt-get install -y --no-install-recommends dialog \
    && apt-get install -y --no-install-recommends openssh-server \
    && echo "root:Docker!" | chpasswd

# sshd should be removed after troubleshoting
COPY azure_deployment/sshd_config /etc/ssh/


RUN pecl list | grep mcrypt || (pecl install mcrypt && docker-php-ext-enable mcrypt)

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer


RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html
COPY . .

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install && \
    npm i && npm run build && \
    chmod 0777 -R /var/www/html && \
    chmod +x azure_deployment/entrypoint.sh && \
    mv azure_deployment/default.conf /etc/nginx/sites-enabled/default && \
    mv azure_deployment/application.ini application/configs/application.ini


EXPOSE 80 2222

ENTRYPOINT ["azure_deployment/entrypoint.sh"]
