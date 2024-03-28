#!/usr/bin/env bash

getVal() {
    param=$1
    from=$2
    current_path=`pwd`
    echo $(awk -F "=" '/'${param}'/ {print $2}' ${current_path}/${from})
}

timestamp=$(date '+%d-%m-%Y@%H:%M')
ROOT_PATH=$(cd $(dirname $0) && pwd)

settings='database.ini'
db_user=$(getVal db_user ${settings})
db_pass=$(getVal db_pass ${settings})
db_host=$(getVal db_host ${settings})
db_port=$(getVal db_port ${settings})
db_name=$(getVal db_name ${settings})

$(mysqldump --user="${db_user}" --password="${db_pass}" --host="${db_host}" --port="${db_port=3306}" ${db_name} -R | gzip > $ROOT_PATH/../../data/dumps/pfleet_dump_$timestamp.sql.gz)