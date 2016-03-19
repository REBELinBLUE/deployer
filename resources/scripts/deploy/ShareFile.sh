[ -f {{ target_file }} ] && cp -pRn {{ target_file }} {{ source_file }} && rm -rf {{ target_file }}
[ ! -f {{ source_file }} ] && touch {{ source_file }}
ln -s {{ source_file }} {{ target_file }}
