### Create shared directories - {{ deployment }}
if [ ! -d {{ shared_dir }}/{{ path }} ]; then
    mkdir -p {{ shared_dir }}/{{ path }}

    if [ -d {{ release_dir }}/{{ path }} ]; then
        cp -pRn {{ release_dir }}/{{ path }} {{ shared_dir }}/{{ parent_dir }}
    fi
fi

if [ -d {{ release_dir }}/{{ path }} ]; then
    rm -rf {{ release_dir }}/{{ path }}
fi

ln -s {{ shared_dir }}/{{ path }} {{ release_dir }}/{{ path }}

if [ $SHARED_NEEDS_MIGRATING ]; then
    if [ -e {{ backup_dir }}/{{ filename }} ]; then
        #rm -rf {{ shared_dir }}/{{ path }}
        cp -RvfT {{ backup_dir }}/{{ filename }} {{ shared_dir }}/{{ path }}
    fi
fi
