cd {{ project_path }}

# Remove the archive
[ -f {{ remote_archive }} ] && rm {{ remote_archive }}

# Remove the release directory
[ -d {{ release_path }} ] && rm -rf {{ release_path }}
