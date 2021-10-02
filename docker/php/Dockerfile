FROM php:7.3-fpm-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apk add --no-cache git less openssh-client bash libpng-dev mysql-client $PHPIZE_DEPS

RUN docker-php-ext-install pdo_mysql gd pcntl \
    && pecl install xdebug-2.9.5 pcov \
    && docker-php-ext-enable pcov

RUN apk del $PHPIZE_DEPS \
    && rm -rf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /application

#COPY php-fpm.conf /usr/local/etc/php-fpm.d/php-fpm.conf
#COPY www.conf /usr/local/etc/php-fpm.d/www.conf
#COPY php-ini-overrides.ini $PHP_INI_DIR/conf.d/99-overrides.ini

CMD ["php-fpm", "-F", "-O"]
