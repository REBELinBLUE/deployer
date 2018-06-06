FROM composer:latest

RUN docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-enable pdo pdo_mysql && \
    apk add --update --no-cache patch && \
    rm -rf /var/cache/apk/*

WORKDIR /application
