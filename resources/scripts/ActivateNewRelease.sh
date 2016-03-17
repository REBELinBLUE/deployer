set -e

cd {{ project_path }}

# Remove the symlink if it already exists
[ -h {{ project_path }}/latest ] && rm -f {{ project_path }}/latest

# Create the new symlink
ln -s {{ release_path }} {{ project_path }}/latest
