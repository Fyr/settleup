#!/bin/sh
set -e
/usr/sbin/sshd
# exec gunicorn -w 4 -b 0.0.0.0:8000 app:app
# Start PHP-FPM in the background
php-fpm -D
# Start nginx in the foreground
nginx -g "daemon off;"
