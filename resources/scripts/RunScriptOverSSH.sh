ssh -o CheckHostIP=no \
    -o IdentitiesOnly=yes \
    -o StrictHostKeyChecking=no \
    -o PasswordAuthentication=no \
    -o IdentityFile={{ private_key }}
    -p {{ port }}
    {{ username }}@{{ ip_address }} 'bash -s' << 'EOF'
        {{ script }}
EOF
