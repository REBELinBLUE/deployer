### Regenerate public SSH key
ssh-keygen -y -f {{ key_file }} | xargs echo -n > {{ key_file }}.pub
echo " deploy@deployer" >> {{ key_file }}.pub
