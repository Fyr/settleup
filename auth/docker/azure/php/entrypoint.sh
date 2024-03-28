#!/bin/bash
set -e
/usr/sbin/sshd
######
echo APP_ENV=$APP_ENV >> /var/www/html/.env
echo APP_KEY=$AUTH_APP_KEY >> /var/www/html/.env
echo DB_HOST=$AUTH_DB_HOST >> /var/www/html/.env
echo DB_DATABASE=$AUTH_DB_DATABASE >> /var/www/html/.env
echo DB_USERNAME=$AUTH_DB_USERNAME >> /var/www/html/.env
echo DB_PASSWORD=$AUTH_DB_PASSWORD >> /var/www/html/.env
echo AZURE_INSIGHT_LOGGER=$AZURE_INSIGHT_LOGGER >> /var/www/html/.env
echo AZURE_INSIGHT_INSTRUMENTATION_KEY=$AZURE_INSIGHT_INSTRUMENTATION_KEY >> /var/www/html/.env
php artisan migrate --force
php artisan db:seed --force
#php-fpm

php-fpm -D
# Start nginx in the foreground
nginx -g "daemon off;"
