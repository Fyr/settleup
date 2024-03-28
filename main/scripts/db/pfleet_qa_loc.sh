#!/bin/bash

dump_pfleet_command="mysqldump -upfleet --password='D@t@b@s3Rulz' pfleet_qa -R | gzip > /tmp/pfleet_qa.sql.gz;"
dump_auth_command="mysqldump -upfleet --password='D@t@b@s3Rulz' pfleet_auth_qa -R | gzip > /tmp/pfleet_auth.sql.gz;"
dump_echo="echo 'dumping...'; "
echo 'connecting...'
ssh pfleet@qa.pfleet.com ${dump_echo}${dump_pfleet_command}${dump_auth_command}
echo 'copying...'
scp pfleet@qa.pfleet.com:"/tmp/pfleet_qa.sql.gz /tmp/pfleet_auth.sql.gz" /tmp/



echo 'droping...'
mysql -uroot -ppassword -e "DROP DATABASE pfleet; CREATE DATABASE pfleet CHARACTER SET utf8 COLLATE utf8_general_ci"
mysql -uroot -ppassword -e "DROP DATABASE pfleet_auth; CREATE DATABASE pfleet_auth CHARACTER SET utf8 COLLATE utf8_general_ci"
echo 'importing...'
gunzip -f < /tmp/pfleet_qa.sql.gz | mysql -uroot -ppassword pfleet
gunzip -f < /tmp/pfleet_auth.sql.gz | mysql -uroot -ppassword pfleet_auth
exit 0;