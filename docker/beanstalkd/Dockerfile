FROM alpine:3.9.6

RUN apk add --no-cache beanstalkd

EXPOSE 11300

ENTRYPOINT ["/usr/bin/beanstalkd"]