#!/bin/bash

dump_pfleet_command="mysqldump -uroot --password='pass' pfleet -R | gzip > /tmp/test_pfleet.sql.gz;"
dump_auth_command="mysqldump -uroot --password='pass' pfleet_auth_qa -R | gzip > /tmp/test_pfleet_auth.sql.gz;"
dump_echo="echo 'dumping...'; "
echo 'connecting...'
ssh 172.18.19.5 ${dump_echo}${dump_pfleet_command}${dump_auth_command}
echo 'copying...'
scp 172.18.19.5:"/tmp/test_pfleet.sql.gz /tmp/test_pfleet_auth.sql.gz" /tmp/



echo 'dropping...'
mysql -uroot -e "DROP DATABASE pfleet; CREATE DATABASE pfleet CHARACTER SET utf8 COLLATE utf8_general_ci"
mysql -uroot -e "DROP DATABASE pfleet_secure; CREATE DATABASE pfleet_secure CHARACTER SET utf8 COLLATE utf8_general_ci"
echo 'importing...'
gunzip -f < /tmp/test_pfleet.sql.gz | mysql -uroot pfleet
gunzip -f < /tmp/test_pfleet_auth.sql.gz | mysql -uroot pfleet_secure
exit 0;