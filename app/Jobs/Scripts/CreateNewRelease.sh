cd {{ project_path }}

# Create the releases directory if it doesn't exist
[ ! -d {{ releases_path }} ] && mkdir {{ releases_path }}

# Create the shared directory if it doesn't exist
[ ! -d {{ shared_path }} ] && mkdir {{ shared_path }}

mkdir {{ release_path }}
cd {{ release_path }}

# Extract the archive
echo -e "\nExtracting...\n"
tar --warning=no-timestamp --gunzip --verbose --extract --file={{ remote_archive }} --directory={{ release_path }}

rm -f {{ remote_archive }}
