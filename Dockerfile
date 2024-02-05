FROM reg.naprocat.com/docker/php-fpm-8.2:latest
COPY ././. /var/www/app
RUN set -eux; \
    chmod -R guo+w /var/www/app/storage

