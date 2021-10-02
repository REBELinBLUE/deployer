FROM php:7.3-alpine

RUN apk add --no-cache git openssh-client bash rsync \
    && docker-php-ext-install pdo_mysql

WORKDIR /application

