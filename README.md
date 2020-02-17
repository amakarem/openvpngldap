# openvpngldap
integrate openvpn with google ldap

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
