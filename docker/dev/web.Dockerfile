FROM sickp/alpine-sshd:7.9-r1

RUN apk add --update --no-cache rsync git bash tar gzip less curl coreutils findutils

RUN addgroup deploy \
    && adduser -D -G deploy -s /bin/bash deploy \
    && passwd -u deploy \
    && chown -R deploy:deploy /home/deploy

RUN mkdir /var/www \
    && chown -R deploy:deploy /var/www

# https://github.com/codecasts/php-alpine
ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

RUN apk --update --no-cach  add ca-certificates && \
    echo "https://dl.bintray.com/php-alpine/v3.11/php-7.2" >> /etc/apk/repositories

RUN apk add --update --no-cache php php-bz2 \
                                php-json php-curl php-gd \
                                php-phar php-iconv php-openssl \
                                php-mbstring php7-fileinfo php7-tokenizer \
                                php-pdo php-pdo_mysql php-zlib

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

USER deploy

WORKDIR /home/deploy

RUN mkdir .composer \
    && chmod 0777 .composer

RUN mkdir .ssh \
    && echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCasyO1qNW+Z331PzHBQ2sgVuvNKZnpUIzz2F+bhS31q2/b2AaXYcU8ljtW2yVcMlUvrvDkRQyynLza2QBfsXTeJpqtKxujqcLXpRN9t81OLjhKebRP/lExq9I6c4xEnwFBx/OqB7ighDNUZc6zRi80V1K3iloGn12ywpL7vI/+EO+ABXP4sTchwh47bppcBNy4HjOre+NqpLNZkZ02E4lngSaOCY6r36TdICaigeQX6n/Xgwm2rRkr0qNIZsd/IoyLYS6/CWUUJjX16qxXt1wwMiwwpRbZ2IULnZ0lI74QXjucD+Ow0OKwWwgLsN55VUGXVOlpX1GJ2p5mZ3H6YX0B deploy@deployer" > .ssh/authorized_keys \
    && chmod 0600 .ssh/authorized_keys

USER root
