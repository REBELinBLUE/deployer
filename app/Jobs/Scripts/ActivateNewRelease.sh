cd {{ project_path }}
[ -h {{ project_path }}/latest ] && rm {{ project_path }}/latest
ln -s {{ release_path }} {{ project_path }}/latest
