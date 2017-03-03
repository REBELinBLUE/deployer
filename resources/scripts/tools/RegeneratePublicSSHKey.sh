### Regenerate public SSH key
ssh-keygen -y -f {{ key_file }} | xargs echo -n > {{ key_file }}.pub
echo " Deployer - {{ project }}" >> {{ key_file }}.pub
