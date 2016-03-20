cd {{ mirror_path }}

chmod +x "{{ wrapper_file }}"
export GIT_SSH="{{ wrapper_file }}"

if [ ! -d {{ mirror_path }} ]; then
    git clone --mirror {{ repository }} {{ mirror_path }}
fi

git fetch --all --prune
