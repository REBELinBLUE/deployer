### Install composer dependencies - {{ deployment }}
cd {{ project_path }}

# If there is no composer file, skip this step
if [ -f {{ release_path }}/composer.json ]; then
    # Check composer is installed
    if type -P composer &> /dev/null; then
        composer="$(command -v composer)"
    else
        # If not, check for composer.phar
        if type -P composer.phar &> /dev/null; then
            composer="$(command -v composer.phar)"
        else
            # If still not, check for composer.phar in the project path
            if [ ! -f {{ project_path }}/composer.phar ]; then
                # Finally, resort to downloading it
                curl -sS https://getcomposer.org/installer | php
                chmod +x composer.phar
            fi

            composer="php {{ project_path }}/composer.phar"
        fi
    fi

    cd {{ release_path }}

    if [ -n "{{ include_dev }}" ]; then
        # Check if the --no-suggest flag exists, from composer >= 1.2
        suggest=""
        if [ $(${composer} help install | grep 'no-suggest' | wc -l) -gt 0 ]; then
            suggest="--no-suggest"
        fi

        ${composer} install --no-interaction --optimize-autoloader \
                          --prefer-dist ${suggest} --no-ansi --working-dir {{ release_path }}


    else
        ${composer} install --no-interaction --optimize-autoloader \
                          --no-dev --prefer-dist --no-suggest --no-ansi --working-dir {{ release_path }}
    fi
fi

cd {{ release_path }}
