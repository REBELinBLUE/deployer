ssh-keygen -y -f {{ private_key }} | xargs echo -n > {{ key_file }}
echo " deploy@deployer" >> {{ key_file }}
