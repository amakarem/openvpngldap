# openvpngldap
integrate openvpn with google ldap

# instlation 
1. ***install os Debian GNU/Linux 9 (stretch)
2. `ssh as root`
3. `apt install git`
4. `apt install apache2`
5. `apt install php`
6. `apt update`
7. `apt install snapd`
8. `snap install core`
9. `snap refresh core`
10. `snap install --classic certbot`
11. `ln -s /snap/bin/certbot /usr/bin/certbot`
12. ***edit apache file to add the domain name ovpn.allheartcare.com using next command

        `nano /etc/apache2/sites-enabled/000-default.conf`

13 `nano /etc/apache2/apache2.conf`
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

14. `service apache2 restart`
15. ****go to http://ovpn.allheartcare.com/ and check if you see apache2 debian page
16. *****point the domain to the server wan IP and be sure that port 80 and 443 are public open
17. `certbot --apache`
18. ***go to http://ovpn.allheartcare.com/ and check if you see apache2 debian page
19. `apt-get install openvpn iptables openssl ca-certificates -y`
20. `wget -O ~/easyrsa.tgz "https://github.com/OpenVPN/easy-rsa/releases/download/v3.0.5/EasyRSA-nix-3.0.5.tgz" 2>/dev/null || curl -Lo ~/easyrsa.tgz "$easy_rsa_url"`
21. `tar xzf ~/easyrsa.tgz -C ~/`
22. `mv ~/EasyRSA-3.0.5/ /etc/openvpn/server/`
23. `mv /etc/openvpn/server/EasyRSA-3.0.5/ /etc/openvpn/server/easy-rsa/`
24. `chown -R root:root /etc/openvpn/server/easy-rsa/`
25. `rm -f ~/easyrsa.tgz`
26. `cd /etc/openvpn/server/easy-rsa/`
27. # Create the PKI, set up the CA and the server and client certificates
	`./easyrsa init-pki`
	`./easyrsa --batch build-ca nopass`
	`EASYRSA_CERT_EXPIRE=3650 ./easyrsa build-server-full server nopass`
	`EASYRSA_CERT_EXPIRE=3650 ./easyrsa build-client-full "client" nopass`
	`EASYRSA_CRL_DAYS=3650 ./easyrsa gen-crl`
28. # Move the stuff we need
	`cp pki/ca.crt pki/private/ca.key pki/issued/server.crt pki/private/server.key pki/crl.pem /etc/openvpn/server`  
29. chown nobody:"$group_name" /etc/openvpn/server/crl.pem
30. # Generate key for tls-crypt
	openvpn --genkey --secret /etc/openvpn/server/tc.key
31. # Create the DH parameters file using the predefined ffdhe2048 group
        `nano /etc/openvpn/server/dh.pem` and enter this code
```
'-----BEGIN DH PARAMETERS-----
MIIBCAKCAQEA//////////+t+FRYortKmq/cViAnPTzx2LnFg84tNpWp4TZBFGQz
+8yTnc4kmz75fS/jY2MMddj2gbICrsRhetPfHtXV/WVhJDP1H18GbtCFY2VVPe0a
87VXE15/V8k1mE8McODmi3fipona8+/och3xWKE2rec1MKzKT0g6eXq8CrGCsyT7
YdEIqUuyyOP7uWrat2DX9GgdT0Kj3jlN9K5W7edjcrsZCwenyO4KbXCeAvzhzffi
7MA0BM0oNC9hkXL+nOmFg/+OTxIy7vKBg8P+OxtMb61zO7X8vC7CIAXFjvGDfRaD
ssbzSibBsu/6iGtCOGEoXJf//////////wIBAg==
-----END DH PARAMETERS-----' >
```
32. # Enable net.ipv4.ip_forward for the system
	`echo 'net.ipv4.ip_forward=1' > /etc/sysctl.d/30-openvpn-forward.conf`
33. # Enable without waiting for a reboot or service restart
	`echo 1 > /proc/sys/net/ipv4/ip_forward`
34. # setup firewall rules
```
if pgrep firewalld; then
		# Using both permanent and not permanent rules to avoid a firewalld
		# reload.
		# We don't use --add-service=openvpn because that would only work with
		# the default port and protocol.
		firewall-cmd --add-port="$port"/"$protocol"
		firewall-cmd --zone=trusted --add-source=10.8.0.0/24
		firewall-cmd --permanent --add-port="$port"/"$protocol"
		firewall-cmd --permanent --zone=trusted --add-source=10.8.0.0/24
		# Set NAT for the VPN subnet
		firewall-cmd --direct --add-rule ipv4 nat POSTROUTING 0 -s 10.8.0.0/24 ! -d 10.8.0.0/24 -j SNAT --to "$ip"
		firewall-cmd --permanent --direct --add-rule ipv4 nat POSTROUTING 0 -s 10.8.0.0/24 ! -d 10.8.0.0/24 -j SNAT --to "$ip"
	else
		# Create a service to set up persistent iptables rules
		echo "[Unit]
Before=network.target
[Service]
Type=oneshot
ExecStart=/sbin/iptables -t nat -A POSTROUTING -s 10.8.0.0/24 ! -d 10.8.0.0/24 -j SNAT --to $ip
ExecStart=/sbin/iptables -I INPUT -p $protocol --dport $port -j ACCEPT
ExecStart=/sbin/iptables -I FORWARD -s 10.8.0.0/24 -j ACCEPT
ExecStart=/sbin/iptables -I FORWARD -m state --state RELATED,ESTABLISHED -j ACCEPT
ExecStop=/sbin/iptables -t nat -D POSTROUTING -s 10.8.0.0/24 ! -d 10.8.0.0/24 -j SNAT --to $ip
ExecStop=/sbin/iptables -D INPUT -p $protocol --dport $port -j ACCEPT
ExecStop=/sbin/iptables -D FORWARD -s 10.8.0.0/24 -j ACCEPT
ExecStop=/sbin/iptables -D FORWARD -m state --state RELATED,ESTABLISHED -j ACCEPT
RemainAfterExit=yes
[Install]
WantedBy=multi-user.target" > /etc/systemd/system/openvpn-iptables.service
		systemctl enable --now openvpn-iptables.service
```

35. `apt install openvpn-auth-ldap`
36. `mkdir /etc/openvpn/auth
37. `nano /etc/openvpn/auth/auth-ldap.conf`
38. **upload google cert files gldap.crt to  /etc/ssl/certs/gldap.crt
39. **upload google keys files gldap.key to /etc/ssl/private/gldap.key
40. `nano /etc/openvpn/auth/auth-ldap.conf` and enter this code
```
<LDAP>
  URL ldaps://ldap.google.com:636 #
  Timeout 15
  TLSEnable false
  TLSCACertDir /etc/ssl/certs
  TLSCertFile /etc/ssl/certs/gldap.crt
  TLSKeyFile /etc/ssl/private/gldap.key
</LDAP>
<Authorization>
  BaseDN "dc=allheartcare,dc=com"
  SearchFilter "(uid=%u)" # (or choose your own LDAP filter for users)
  RequireGroup false
</Authorization>
```

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
