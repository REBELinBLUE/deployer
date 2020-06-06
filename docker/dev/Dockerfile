FROM sickp/alpine-sshd:7.9-r1

RUN apk add --update --no-cache git bash less

RUN addgroup -S deploy \
    && adduser -D -G deploy -s /bin/bash deploy \
    && passwd -u deploy \
    && chown -R deploy:deploy /home/deploy

RUN mkdir /var/www \
    && chown -R deploy:deploy /var/www

USER deploy

WORKDIR /home/deploy

RUN mkdir .ssh \
    && echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCasyO1qNW+Z331PzHBQ2sgVuvNKZnpUIzz2F+bhS31q2/b2AaXYcU8ljtW2yVcMlUvrvDkRQyynLza2QBfsXTeJpqtKxujqcLXpRN9t81OLjhKebRP/lExq9I6c4xEnwFBx/OqB7ighDNUZc6zRi80V1K3iloGn12ywpL7vI/+EO+ABXP4sTchwh47bppcBNy4HjOre+NqpLNZkZ02E4lngSaOCY6r36TdICaigeQX6n/Xgwm2rRkr0qNIZsd/IoyLYS6/CWUUJjX16qxXt1wwMiwwpRbZ2IULnZ0lI74QXjucD+Ow0OKwWwgLsN55VUGXVOlpX1GJ2p5mZ3H6YX0B deploy@deployer" > .ssh/authorized_keys \
    && chmod 0600 .ssh/authorized_keys

USER root
