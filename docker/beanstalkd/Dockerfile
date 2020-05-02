FROM alpine:3.6.9

RUN apk add --no-cache beanstalkd

EXPOSE 11300

ENTRYPOINT ["/usr/bin/beanstalkd"]