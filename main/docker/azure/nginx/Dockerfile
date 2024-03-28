FROM nginx:latest

# install node and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Copy vhost
COPY ./docker/azure/nginx/vhost.conf /etc/nginx/templates/default.conf.template

# Copy app data
COPY . /var/www/html

WORKDIR /var/www/html

RUN npm i && \
    npm run build

#SSH stack
# COPY ./docker/azure/nginx/entrypoint.sh /entrypoint.sh
# COPY ./docker/azure/nginx/sshd_config /etc/ssh/
# RUN apt-get update \
#     && apt-get install -y --no-install-recommends dialog \
#     && apt-get install -y --no-install-recommends openssh-server \
#     && echo "root:Docker!" | chpasswd \
#     && chmod +x /entrypoint.sh

# EXPOSE 2222 80 443

# CMD ["/entrypoint.sh"]