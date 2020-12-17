# openvpngldap
integrate openvpn with google ldap

# instlation 
* ***install os Debian GNU/Linux 9 (stretch)
1. `ssh as root`
2. `apt install git`
3. `apt install apache2`
4. `apt install php`
5. `apt update`
6. `apt install snapd`
7. `snap install core`
8. `snap refresh core`
9. `snap install --classic certbot`
10. `ln -s /snap/bin/certbot /usr/bin/certbot`
* ***edit apache file to add the domain name ovpn.allheartcare.com using next command

`nano /etc/apache2/sites-enabled/000-default.conf`

`nano /etc/apache2/apache2.conf`
* **** chenage this 
```
<Directory /var/www>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>
```
* *** to this 
```
<Directory /var/www>
        Options -Indexes +FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>
```

`service apache2 restart`

* ****go to http://ovpn.allheartcare.com/ and check if you see apache2 debian page

* *****point the domain to the server wan IP and be sure that port 80 and 443 are public open

`certbot --apache`


# Setup
1. `sudo -s`
2. `git clone https://github.com/amakarem/openvpngldap`
3. `openvpn-install.sh`
4. `apt install openvpn-auth-ldap`
5. `mk dir /etc/openvpn/auth`
6. `cp ./config-sample/auth-ldap.conf /etc/openvpn/auth/auth-ldap.conf`
7. edit this file /etc/openvpn/auth/auth-ldap.conf with google cert and key file location after upload them to the server
`vi /etc/openvpn/auth/auth-ldap.conf`
8. edit the openvpn server.conf file located it `/etc/openvpn/server/server.conf` same as the sample
you can do this by run `cp ./config-sample/server.conf /etc/openvpn/server/server.conf`
9. download the client.ovpn file that is created from step number 3 to be same as the sample client file 
