#!/bin/bash
set -e

# CONFIG
## THE SCRIPT USES THE FOLLOWING ENVIRONMENT VARIABLES:
### MYSQL_DATABASE
### MYSQL_USER
### MYSQL_PASSWORD

# DUMP FILE
## PLEASE MAKE SURE THAT THE DB DUMP EXISTS IN THE FOLLOWING PATH
### /var/scripts/db/pfleet_dump.sql


echo ">>> Import initial data into the ${MYSQL_DATABASE} database."
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < /var/scripts/db/pfleet_dump.sql
echo ">>> Initial data import has been finished successfully."
echo ">>> Apply DB migrations"
cd /var/scripts/db/ && bash migrate.sh
echo ">>> DB migrations were successfully applied"
