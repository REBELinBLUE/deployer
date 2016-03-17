rsync --verbose --compress --progress --out-format="Receiving %%n" -e "ssh -p %s ' .
            '-o CheckHostIP=no ' .
            '-o IdentitiesOnly=yes ' .
            '-o StrictHostKeyChecking=no ' .
            '-o PasswordAuthentication=no ' .
            '-i %s" ' .
            '%s %s@%s:%s',
            $server->port,
            $this->private_key,
            $local_file,
            $server->user,
            $server->ip_address,
            $remote_file
