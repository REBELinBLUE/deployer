### Create release archive - {{ deployment }}
git clone --depth 1 --recursive {{ mirror_path }} {{ tmp_path }}
cd {{ tmp_path }}
git checkout {{ sha }}
{{ scripts_path }}tools/GitArchiveAll.sh --tree-ish {{ sha }} --format tar.gz --verbose {{ release_archive }}
cd -
rm -rf {{ tmp_path }}
