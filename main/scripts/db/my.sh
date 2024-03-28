#!/bin/bash
echo "create DB start"
WORKDIR=${1:-$PWD}
DIR="${WORKDIR}/patches"

echo "initial file - pfleet.sql"
mysql -uroot -p1 settlement < "${WORKDIR}/pfleet.sql"

find $DIR -type f -iname "*.sql" | sort -n | while read FILENAME; do
echo "update to $FILENAME"
mysql -uroot -p1 settlement < $FILENAME
done
echo "create DB finish"
