FROM mysql:5.7

ADD ./docker/mysql/db/db-create.sh /docker-entrypoint-initdb.d/
RUN chmod 755 /docker-entrypoint-initdb.d/db-create.sh