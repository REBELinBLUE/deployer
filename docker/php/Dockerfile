FROM php:7.2.30-fpm-alpine3.11

RUN apk add --no-cache git openssh-client bash libpng-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql gd \
    && pecl install xdebug-2.9.5 \
    && apk del $PHPIZE_DEPS \
    && rm -rf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR "/application"

CMD ["php-fpm", "-F", "-O"]