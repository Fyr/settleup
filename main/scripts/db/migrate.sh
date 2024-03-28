#!/usr/bin/env bash

# ----------------------------------------------------------------
# MigBash::Migrate - implements simple migrations for your project
# ----------------------------------------------------------------

# Retrieve value for provided parameter
# from provided configuration file
MigBash::getVal() {
    param=$1
    from=$2
    current_path=`pwd`
    echo $(awk -F "=" '/'${param}'/ {print $2}' ${current_path}/${from})
}

# Shift position
MigBash::margin() {
    position=${1}
    echo $(tput hpa ${position})
}

# check if database exists
db_exists() {
    local db_query=$(mysql --user="${db_user}" --password="${db_pass}" --host="${db_host}" --port="${db_port=3306}" -e "SHOW DATABASES LIKE '${1}';")
    echo ${db_query} | awk '{print $3}'
}

# check if table exists
table_exists() {
    local db_query=$(mysql --user="${db_user}" --password="${db_pass}" --host="${db_host}" --port="${db_port=3306}" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${1}' AND table_name = '${2}';")
    echo ${db_query} | awk '{print $2}'
}

# create table for migrations
create_table() {
    local db_query=$(mysql --user="${db_user}" --password="${db_pass}" --host="${db_host}" ${db_name} -e "
    CREATE TABLE ${migration_table} (
            id INT(10) NOT NULL AUTO_INCREMENT,
            name CHAR(60) NULL DEFAULT NULL,
            date CHAR(60) NULL DEFAULT NULL,
            PRIMARY KEY (id)
    ) COLLATE='utf8_unicode_ci' ENGINE=InnoDB;
    ")
}

# check whether the patch was applied or not
is_applied() {
    local patch=$(mysql --user="${db_user}" --password="${db_pass}" --host="${db_host}" ${db_name} -e "SELECT COUNT(*) FROM ${migration_table} WHERE name = '${1}';")
    echo ${patch} | awk '{print $2}'
}

# apply patch
apply_patch() {
    local db_query=$(mysql --user="${db_user}" --password="${db_pass}" --host="${db_host}" ${db_name} < "${1}")
}

# log patch into migration's table
log_patch() {
    $(mysql --user="${db_user}" --password="${db_pass}" --host="${db_host}" ${db_name} -e "INSERT INTO ${migration_table} (name, date) VALUES ('${1}', '${date}');")
}

# check if provided folder exists
folder_exists() {
    if [ ! -d ${1} ]
    then
        echo 1
    fi
}

# apply migrations
start_migrate() {
    local folder_exists=$(folder_exists ${patches_path})
    if [[ ${folder_exists} -ne 0 ]]
    then
        echo -e "Folder does not exists: ${patches_path}\n"
    else
        for PATCH in $(ls ${patches_path})
        do
            local patch_applied=$(is_applied "${PATCH}")
            if [[ ${patch_applied} -ne 0 ]]
            then
                echo -e "${PATCH} - skipped"
            else
                $(apply_patch ${patches_path}${PATCH})
                if [[ $? -ne 0 ]]
                then
                    echo -e "${PATCH} - Failed..."
                else
                    $(log_patch "${PATCH}")
                    echo -e "${PATCH} - Ok!"
                fi
            fi
        done
    fi
}

MigBash::Migrate() {
    local settings='database.ini'
    db_user=$(MigBash::getVal db_user ${settings})
    db_pass=$(MigBash::getVal db_pass ${settings})
    db_host=$(MigBash::getVal db_host ${settings})
    db_port=$(MigBash::getVal db_port ${settings})
    db_name=$(MigBash::getVal db_name ${settings})

    patches_path=$(MigBash::getVal patches ${settings})
    procedures_path=$(MigBash::getVal procedures ${settings})

    local migration_table='tbl_migrations'
    local date=`date '+%Y-%m-%d %H:%M:%S'`

    local db_exists=$(db_exists ${db_name})
    if [[ ! ${db_exists} ]]
    then
        echo "Provided database doesn't exists..." >&2
        exit 1
    fi

    local tbl_exists=$(table_exists ${db_name} ${migration_table})

    if [[ ${tbl_exists} -ne 0 ]]
    then
       start_migrate
    else
       $(create_table ${migration_table})
       start_migrate
    fi
}

MigBash::applyProcedures() {
    $(apply_patch ${procedures_path})
}

MigBash::Migrate
MigBash::applyProcedures
