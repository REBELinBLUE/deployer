FROM prooph/composer:7.3

RUN composer global require ergebnis/composer-normalize && \
    composer self-update --1

RUN apk add --update --no-cache git openssh patch libpng-dev $PHPIZE_DEPS && \
    docker-php-ext-install gd pdo_mysql && \
    pecl install pcov && \
    docker-php-ext-enable pcov && \
    apk del $PHPIZE_DEPS

WORKDIR /application
