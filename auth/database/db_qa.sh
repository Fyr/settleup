#!/bin/bash
echo "Drop DB pfleet_auth_qa"
echo "DROP DATABASE IF EXISTS pfleet_auth_qa;" | mysql -uroot -ppass
echo "Create DB pfleet_auth_qa"
echo "CREATE DATABASE pfleet_auth_qa CHARACTER SET utf8 COLLATE utf8_bin" | mysql -uroot -ppass
echo "SET FOREIGN_KEY_CHECKS=0;" | mysql -uroot -ppass
echo "Import migrations"
php artisan migrate
php artisan db:seed