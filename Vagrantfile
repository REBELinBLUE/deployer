Vagrant.require_version ">= 1.5.0"

Vagrant.configure("2") do |config|
    # Configure the box
    config.vm.box = "laravel/homestead"
    config.vm.hostname = "deployer"
    config.vm.box_check_update = true

    # Configure SSH
    config.ssh.forward_agent = true

    # Prevent TTY Errors
    config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

    # Configure a private network IP
    config.vm.network :private_network, ip: "192.168.10.10"

    # Configure VirtualBox settings
    config.vm.provider "virtualbox" do |provider|
        provider.name = "deployer"
        provider.customize ["modifyvm", :id, "--memory", 2048]
        provider.customize ["modifyvm", :id, "--cpus", 1]
        provider.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
        provider.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        provider.customize ["modifyvm", :id, "--ostype", "Ubuntu_64"]
    end

    # Configure port forwarding to the box
    config.vm.network "forwarded_port", guest: 80, host: 8000, auto_correct: true
    config.vm.network "forwarded_port", guest: 443, host: 44300, auto_correct: true
    config.vm.network "forwarded_port", guest: 3306, host: 33060, auto_correct: true

    config.vm.synced_folder "./", "/var/www/deployer"

    # Configure The Public Key For SSH Access
    config.vm.provision "shell" do |s|
        s.inline = "echo $1 | grep -xq \"$1\" /home/vagrant/.ssh/authorized_keys || echo $1 | tee -a /home/vagrant/.ssh/authorized_keys"
        s.args = [File.read(File.expand_path("~/.ssh/id_rsa.pub"))]
    end

    # Copy The SSH Private Keys To The Box
    config.vm.provision "shell" do |s|
        s.privileged = false
        s.inline = "echo \"$1\" > /home/vagrant/.ssh/id_rsa && chmod 600 /home/vagrant/.ssh/id_rsa"
        s.args = [File.read(File.expand_path("~/.ssh/id_rsa"))]
    end

    config.vm.provision "file", source: "~/.gitconfig", destination: "~/.gitconfig"
    config.vm.provision "file", source: "~/.composer/auth.json", destination: "~/.composer/auth.json"

    # Provision
    config.vm.provision "shell", inline: "sudo bash /vagrant/examples/dev/provision.sh"

    # Update composer on each boot
    config.vm.provision "shell", inline: "sudo /usr/local/bin/composer self-update", run: "always"
end
