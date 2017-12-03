FROM webdevops/php-nginx:alpine-php7

COPY main.conf /opt/docker/etc/nginx
COPY default.conf /opt/docker/etc/nginx
COPY 04-compose.sh /opt/docker/provision/entrypoint.d