#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

# Remove postgresql
apt-get remove postgresql-9.4 postgresql-client-9.4 postgresql-common -y
apt-get autoremove

rm -rf /var/log/postgresql
rm -rf /etc/postgresql-common
rm -rf /etc/postgresql
rm -rf /var/run/postgresql
rm -rf /var/lib/postgresql

# Install MariaDB
DEBIAN_FRONTEND=noninteractive apt-get remove -y --purge mysql-server mysql-client mysql-common
DEBIAN_FRONTEND=noninteractive apt-get autoremove -y
DEBIAN_FRONTEND=noninteractive apt-get autoclean

rm -rf /var/lib/mysql
rm -rf /var/log/mysql
rm -rf /etc/mysql

apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db
add-apt-repository 'deb [arch=amd64,i386] http://ftp.osuosl.org/pub/mariadb/repo/10.1/ubuntu trusty main'
apt-get update

debconf-set-selections <<< "mariadb-server-10.1 mysql-server/data-dir select ''"
debconf-set-selections <<< "mariadb-server-10.1 mysql-server/root_password password secret"
debconf-set-selections <<< "mariadb-server-10.1 mysql-server/root_password_again password secret"

DEBIAN_FRONTEND=noninteractive apt-get install -y mariadb-server

echo "default_password_lifetime = 0" >> /etc/mysql/my.cnf

sed -i '/^bind-address/s/bind-address.*=.*/bind-address = 0.0.0.0/' /etc/mysql/my.cnf

mysql -uroot -psecret  -e "GRANT ALL ON *.* TO root@'0.0.0.0' IDENTIFIED BY 'secret' WITH GRANT OPTION;"

service mysql restart

mysql -uroot -psecret  -e "CREATE USER 'homestead'@'0.0.0.0' IDENTIFIED BY 'secret';"
mysql -uroot -psecret  -e "GRANT ALL ON *.* TO 'homestead'@'0.0.0.0' IDENTIFIED BY 'secret' WITH GRANT OPTION;"
mysql -uroot -psecret  -e "GRANT ALL ON *.* TO 'homestead'@'%' IDENTIFIED BY 'secret' WITH GRANT OPTION;"
mysql -uroot -psecret  -e "FLUSH PRIVILEGES;"

service mysql restart

# Install github changelog generator
apt-get update

apt-add-repository ppa:brightbox/ruby-ng
apt-get update -y
DEBIAN_FRONTEND=noninteractive apt-get install ruby2.3 ruby2.3-dev httpie -y
gem install github_changelog_generator

# Create DB
mysql -uhomestead -psecret -e "DROP DATABASE IF EXISTS deployer;"
mysql -uhomestead -psecret -e "CREATE DATABASE deployer DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;"

# Install JS CS, redis commander & diff-so-fancy
npm install -g redis-commander jscs diff-so-fancy

# Install beanstalk console
[ ! -d /var/www/beanstalk ] && composer create-project ptrofimov/beanstalk_console -q -n -s dev /var/www/beanstalk
[ -d /var/www/html ] && rm -rf /var/www/html
chown -R vagrant:vagrant /var/www/beanstalk

# Copy dev tools config
cp /var/www/deployer/examples/dev/nginx.conf /etc/nginx/sites-available/tools.conf
ln -fs /etc/nginx/sites-available/tools.conf /etc/nginx/sites-enabled/tools.conf
cp /var/www/deployer/examples/dev/redis-commander.conf /etc/supervisor/conf.d/redis-commander.conf

# Copy deployer supervisor and cron config
cp /var/www/deployer/examples/supervisor.conf /etc/supervisor/conf.d/deployer.conf
cp /var/www/deployer/examples/crontab /etc/cron.d/deployer
cp /var/www/deployer/examples/nginx.conf /etc/nginx/sites-available/deployer.conf
ln -fs /etc/nginx/sites-available/deployer.conf /etc/nginx/sites-enabled/deployer.conf

# Restart services
service redis-server restart
service beanstalkd restart
service supervisor restart
service nginx restart
service cron restart
service php7.0-fpm restart

# Stop composer complaining - phpdismod -s cli xdebug isn't working
rm /etc/php/7.0/cli/conf.d/20-xdebug.ini

# Update .profile
echo 'alias php="php -dzend_extension=xdebug.so"' >> /home/vagrant/.profile
echo 'alias artisan="php artisan"' >> /home/vagrant/.profile
echo 'alias phpunit="php $(which phpunit)"' >> /home/vagrant/.profile
echo 'export PATH=/var/www/deployer/vendor/bin:$PATH' >> /home/vagrant/.profile
echo 'cd /var/www/deployer' >> /home/vagrant/.profile
