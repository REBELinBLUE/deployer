FROM phpdockerio/php72-fpm:latest

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y remove php5.6 \
    && apt-get -y autoremove \
    && apt-get -y --no-install-recommends install php7.2-fpm php7.2-cli php7.2-mysql php7.2-sqlite3 php7.2-readline \
                                                  php7.2-gd php7.2-curl php7.2-mbstring php7.2-phpdbg \
                                                  php-xhprof php-xdebug \
    && apt-get -y install ssh \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    && phpdismod xdebug

WORKDIR "/application"
