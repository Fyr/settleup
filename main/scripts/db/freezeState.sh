#!/usr/bin/env bash
#
# P-Fleet
# The script saves database state with stored 
# procedures and restores them on demand
#--------------------------------------------

# get value from configuration file
PFleet::getVal() {
    param=$1
    from=$2
    current_path=`pwd`
    echo $(awk -F "=" '/'${param}'/ {print $2}' ${current_path}/${from})
}

# read configs
PFleet::readConfig() {
    settings='../scripts/db/database.ini'
    db_user=$(PFleet::getVal db_user ${settings})
    db_pass=$(PFleet::getVal db_pass ${settings})
    db_host=$(PFleet::getVal db_host ${settings})
    db_port=$(PFleet::getVal db_port ${settings})
    db_name=$(PFleet::getVal db_name ${settings})
}

# check if database exists
PFleet::db_exists() {
    local db_query=$(mysql --user="${db_user}" --password="${db_pass}" --host="${db_host}" --port="${db_port=3306}" -e "SHOW DATABASES LIKE '${1}';")
    echo ${db_query} | awk '{print $3}'
    exit 1
}

# save database state in gzipped format
PFleet::saveState() {
    state_name=${1}
    local db_exists=$(PFleet::db_exists ${db_name})
    if [[ ! ${db_exists} ]]
    then
        echo "Provided database doesn't exists..." >&2
        exit 1
    fi

    # backup db in silence mode
    mysqldump --user="${db_user}" --password="${db_pass}" --host="${db_host=127.0.0.1}" --port="${db_port=3306}" ${db_name} -R | gzip > ${state_name}.sql.gz
}

# restore provided database state from selected archive
PFleet::restoreState() {
    state_name=${1}
    gunzip < ${state_name} | mysql --user="${db_user}" --password="${db_pass}" --host="${db_host=127.0.0.1}" --port="${db_port=3306}" ${db_name}
}

# FreezeState initialization
PFleet::init() {
    PFleet::readConfig
    action=${1}
    state=${2}
    if [[ "${action}" == "save" ]]; then
        PFleet::saveState ${2}
    elif [[ "${action}" == "restore" ]]; then
        PFleet::restoreState ${2}
    else
        echo "\"${action}\" - action is not supported"
    fi
}

PFleet::init ${1} ${2}