FROM nielsvdoorn/laravel-supervisor

RUN apk --update --no-cache add openssh bash rsync && \
    mkdir -p /var/log/supervisor/

WORKDIR /application
