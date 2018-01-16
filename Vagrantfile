Vagrant.configure("2") do |config|

    config.vm.provision "fix-no-tty", type: "shell" do |s|
        s.privileged = false
        s.inline = "sudo sed -i '/tty/!s/mesg n/tty -s \\&\\& mesg n/' /root/.profile"
    end

    # config.ssh.username = "vagrant"
    # config.ssh.password = "vagrant"
    config.vm.box = "scotch/box"
    # config.vm.box_version = "2.5"
    config.vm.provider :virtualbox do |vb|
        vb.customize ["modifyvm", :id, "--memory", "2096"]
        vb.customize ["modifyvm", :id, "--cpus", "2"]   
    end

    config.vm.network "private_network", ip: "192.168.88.6"
    config.vm.hostname = "scotchbox12"
    config.vm.synced_folder ".", "/var/www", type: "rsync",
        rsync__exclude: ["app/", "bin/", "src/", "tests/", "vendor/", "web/"]

    config.vm.synced_folder "app/", "/var/www/app", type: "nfs"
    config.vm.synced_folder "bin/", "/var/www/bin", type: "nfs"
    config.vm.synced_folder "src/", "/var/www/src", type: "nfs"
    config.vm.synced_folder "tests/", "/var/www/tests", type: "nfs"
    config.vm.synced_folder "vendor/", "/var/www/vendor", type: "nfs"
    config.vm.synced_folder "web/", "/var/www/web", type: "nfs"

    # Change the document root folder
    config.vm.provision "shell", privileged: false, inline: <<-SHELL
        sudo ln -s /home/vagrant/.rbenv/shims/ruby /usr/bin/ruby
        sudo /home/vagrant/.rbenv/shims/gem install compass
        sudo ln -s /home/vagrant/.rbenv/shims/compass /usr/bin/compass
        sudo ln -s /home/vagrant/.rbenv/shims/sass /usr/bin/sass
        sudo sed -i s,/var/www/public,/var/www/web,g /etc/apache2/sites-available/000-default.conf
        sudo sed -i s,/var/www/public,/var/www/web,g /etc/apache2/sites-available/scotchbox.local.conf
        sudo service apache2 restart
    SHELL
end

