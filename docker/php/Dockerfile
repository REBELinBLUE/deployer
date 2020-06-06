FROM php:7.2.30-fpm-alpine3.11

RUN apk add --no-cache git less openssh-client bash libpng-dev mysql-client $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql gd pcntl \
    && pecl install xdebug-2.9.5 pcov \
    && docker-php-ext-enable pcov \
    && apk del $PHPIZE_DEPS \
    && rm -rf /usr/local/etc/php-fpm.d/zz-docker.confdoco

WORKDIR /application

#COPY php-fpm.conf /usr/local/etc/php-fpm.d/php-fpm.conf
#COPY www.conf /usr/local/etc/php-fpm.d/www.conf
#COPY php-ini-overrides.ini /etc/php/7.2/fpm/conf.d/99-overrides.ini

CMD ["php-fpm", "-F", "-O"]
