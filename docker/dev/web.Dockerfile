FROM debian:bullseye

# https://github.com/dotcloud/docker/issues/1024
RUN dpkg-divert --local --rename --add /sbin/initctl
RUN ln -s /bin/true /sbin/initctl

# install mariadb
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y wget openssh-server supervisor git curl

RUN mkdir -p /var/run/sshd && mkdir -p /var/log/supervisor

ADD supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN addgroup deploy \
    && adduser --disabled-password --gecos '' --ingroup deploy --quiet --shell /bin/bash deploy \
    && passwd -u deploy \
    && chown -R deploy:deploy /home/deploy


RUN apt-get install -y openssh-server git curl \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir /var/www \
    && chown -R deploy:deploy /var/www

## https://github.com/codecasts/php-alpine
#ADD https://packages.whatwedo.ch/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub
#
#RUN apk --update --no-cach  add ca-certificates && \
#    echo "https://dl.bintray.com/php-alpine/v3.11/php-7.2" >> /etc/apk/repositories
#
#RUN apk add --update --no-cache php php-bz2 \
#                                php-json php-curl php-gd \
#                                php-phar php-iconv php-openssl \
#                                php-mbstring php7-fileinfo php7-tokenizer \
#                                php-pdo php-pdo_mysql php-zlib
#
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
#
USER deploy

WORKDIR /home/deploy

RUN mkdir .composer && chmod 0777 .composer

RUN mkdir .ssh \
    && echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCasyO1qNW+Z331PzHBQ2sgVuvNKZnpUIzz2F+bhS31q2/b2AaXYcU8ljtW2yVcMlUvrvDkRQyynLza2QBfsXTeJpqtKxujqcLXpRN9t81OLjhKebRP/lExq9I6c4xEnwFBx/OqB7ighDNUZc6zRi80V1K3iloGn12ywpL7vI/+EO+ABXP4sTchwh47bppcBNy4HjOre+NqpLNZkZ02E4lngSaOCY6r36TdICaigeQX6n/Xgwm2rRkr0qNIZsd/IoyLYS6/CWUUJjX16qxXt1wwMiwwpRbZ2IULnZ0lI74QXjucD+Ow0OKwWwgLsN55VUGXVOlpX1GJ2p5mZ3H6YX0B deploy@deployer" > .ssh/authorized_keys \
    && chmod 0600 .ssh/authorized_keys

USER root

CMD ["/usr/bin/supervisord", "-n"]
