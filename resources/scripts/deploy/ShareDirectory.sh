[ -d {{ target_file }} ] && cp -pRn {{ target_file }} {{ source_file }} && rm -rf {{ target_file }}
[ ! -d {{ source_file }} ] && mkdir {{ source_file }}
ln -s {{ source_file }} {{ target_file }}
