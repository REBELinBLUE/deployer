### Create shared files - {{ deployment }}
mkdir -p {{ shared_dir }}/{{ parent_dir }}

if [ -f {{ release_dir }}/{{ path }} ]; then
    if [ ! -f {{ shared_dir }}/{{ path }} ]; then
        cp -pRn {{ release_dir }}/{{ path }} {{ shared_dir }}/{{ path }}
    fi

    rm -f {{ release_dir }}/{{ path }}
fi

if [ ! -f {{ shared_dir }}/{{ path }} ]; then
    touch {{ shared_dir }}/{{ path }}
fi

ln -s {{ shared_dir }}/{{ path }} {{ release_dir }}/{{ path }}

if [ $SHARED_NEEDS_MIGRATING ]; then
    if [ -e {{ backup_dir }}/{{ filename }} ]; then
        rm -rf {{ shared_dir }}/{{ path }}
        cp -RvfT {{ backup_dir }}/{{ filename }} {{ shared_dir }}/{{ path }}
    fi
fi
