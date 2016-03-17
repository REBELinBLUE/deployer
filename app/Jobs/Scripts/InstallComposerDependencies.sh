# If there is no composer file, skip this step
[ ! -f {{ release_path }}/composer.json ] && exit 0
cd {{ project_path }}

# If composer isn't installed check for composer.phar
# Then check for the phar in the root dir, if not then
# download it and then set an alias
composer="$(command -v composer)"
if ! hash composer 2>/dev/null; then
    composer="$(command -v composer.phar)"

    if ! hash composer.phar 2>/dev/null; then
        if [ ! -f {{ project_path }}/composer.phar ]; then
            curl -sS https://getcomposer.org/installer | php
            chmod +x composer.phar
        fi

        composer="php {{ project_path }}/composer.phar"
    fi
fi

cd {{ release_path }}

if [ -n "{{ include_dev }}" ]; then
    $composer install --no-interaction --optimize-autoloader \
                      --prefer-dist --no-ansi --working-dir "{{ release_path }}"
else
    $composer install --no-interaction --optimize-autoloader \
                      --no-dev --prefer-dist --no-ansi --working-dir "{{ release_path }}"
fi
