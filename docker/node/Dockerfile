FROM node:12.16.3-alpine

RUN apk add --no-cache --update supervisor && mkdir -p /var/log/supervisor

WORKDIR /application

#COPY supervisord.conf /etc/supervisord.conf

EXPOSE 6001

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
