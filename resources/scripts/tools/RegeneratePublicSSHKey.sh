### Regenerate public SSH key
if [ -f {{ key_file }}.pub ]; then
    rm -f {{ key_file }}.pub
fi

ssh-keygen -y -f {{ key_file }} | xargs echo -n > {{ key_file }}.pub
echo " deploy@deployer" >> {{ key_file }}.pub
