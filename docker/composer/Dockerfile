FROM prooph/composer:7.2

RUN composer global require ergebnis/composer-normalize && \
    composer self-update

RUN docker-php-ext-install pdo pdo_mysql && \
    apk add --update --no-cache patch

WORKDIR /application
