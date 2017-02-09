### Activate new release - {{ deployment }}
cd {{ project_path }}

# Remove the symlink if it already exists
if [ -h {{ project_path }}/latest ]; then
    rm -f {{ project_path }}/latest
fi

# Create the new symlink
ln -s {{ release_path }} {{ project_path }}/latest

# Restart php-fpm
if [ ! -z "$(ps -ef | grep -v grep | grep php-fpm)" ]; then
    sudo /usr/sbin/service php5-fpm restart
fi

# FIXME: Need some work, there are many different ways http://serverfault.com/questions/189940/how-do-you-restart-php-fpm
