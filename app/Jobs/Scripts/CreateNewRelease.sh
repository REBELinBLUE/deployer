cd {{ project_path }}
[ ! -d {{ releases_path }} ] && mkdir {{ releases_path }}
[ ! -d {{ shared_path }} ] && mkdir {{ shared_path }}
mkdir {{ release_path }}
cd {{ release_path }}
echo -e "\nExtracting...\n"
tar --warning=no-timestamp --gunzip --verbose --extract --file={{ remote_archive }} --directory={{ release_path }}
rm {{ remote_archive }}
