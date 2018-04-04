FROM node:alpine

RUN \
    echo -e 'http://dl-cdn.alpinelinux.org/alpine/edge/main\nhttp://dl-cdn.alpinelinux.org/alpine/edge/community\nhttp://dl-cdn.alpinelinux.org/alpine/edge/testing' >> /etc/apk/repositories \
    && apk --no-cache --update add g++ yarn supervisor make \
    && mkdir -p /etc/supervisor/conf.d \
    && mkdir /var/log/supervisor \
    && rm -rf /var/cache/apk/* \
    && npm install -g gulp bower

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
