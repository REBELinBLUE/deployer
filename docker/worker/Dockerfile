FROM php:7.2.30-alpine3.11

RUN apk add --no-cache git openssh-client bash rsync supervisor\
    && mkdir -p /etc/supervisor/conf.d \
    && mkdir -p /var/log/supervisor \
    && docker-php-ext-install pdo_mysql

WORKDIR /application

#COPY supervisord.conf /etc/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
