#!/bin/bash
echo "export current DB to pfleet_qa.sql "
mysqldump -uroot -ppass --opt pfleet -R > pfleet_qa.sql
echo "Drop DB pfleet_qa"
echo "DROP DATABASE IF EXISTS pfleet_qa;" | mysql -uroot -ppass
echo "Create DB pfleet_qa"
echo "CREATE DATABASE pfleet_qa CHARACTER SET utf8 COLLATE utf8_bin" | mysql -uroot -ppass
echo "SET FOREIGN_KEY_CHECKS=0;" | mysql -uroot -ppass
echo "Import DB pfleet_qa"
mysql -uroot -ppass pfleet_qa < pfleet_qa.sql
echo "SET FOREIGN_KEY_CHECKS=1;" | mysql -uroot -ppass
echo "Import  pfleet_qa complete"