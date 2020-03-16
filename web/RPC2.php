<?php
function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) {
        return '';
    }

    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$request = file_get_contents('php://input');
$request = get_string_between($request, '<methodName>', '</methodName>');
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $user = $_SERVER['PHP_AUTH_USER'];
    $server = $_SERVER['HTTP_HOST'];
    header("X-Frame-Options: SAMEORIGIN");
    header("Content-Type: text/xml");
    if ($request == 'GetSession') {
        $user = base64_encode($user);
        echo "<?xml version='1.0'?>
    <methodResponse>
    <params>
    <param>
    <value><struct>
    <member>
    <name>status</name>
    <value><int>0</int></value>
    </member>
    <member>
    <name>session_id</name>
    <value><string>$user</string></value>
    </member>
    </struct></value>
    </param>
    </params>
    </methodResponse>
    ";
    } elseif ($request == 'GetUserlogin') {
        $user = $_SERVER["PHP_AUTH_PW"];
        $user = base64_decode($user);
echo "<?xml version='1.0'?>
<methodResponse>
<params>
<param>
<value><string># Automatically generated OpenVPN client config file
# Generated on Tue Feb 18 14:42:41 2020 by $server

# Default Cipher
cipher AES-256-CBC
# Note: this config file contains inline private keys
#       and therefore should be kept confidential!
# Note: this configuration is user-locked to the username below
# OVPN_ACCESS_SERVER_USERNAME=ahmed.e
# Define the profile name of this particular configuration file
# OVPN_ACCESS_SERVER_PROFILE=ahmed.e@$server
# OVPN_ACCESS_SERVER_CLI_PREF_ALLOW_WEB_IMPORT=True
# OVPN_ACCESS_SERVER_CLI_PREF_BASIC_CLIENT=False
# OVPN_ACCESS_SERVER_CLI_PREF_ENABLE_CONNECT=False
# OVPN_ACCESS_SERVER_CLI_PREF_ENABLE_XD_PROXY=True
# OVPN_ACCESS_SERVER_WSHOST=$server:443
# OVPN_ACCESS_SERVER_WEB_CA_BUNDLE_START
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
# -----END CERTIFICATE-----
# OVPN_ACCESS_SERVER_WEB_CA_BUNDLE_STOP
# OVPN_ACCESS_SERVER_IS_OPENVPN_WEB_CA=0
# setenv FORWARD_COMPATIBLE 1
client
server-poll-timeout 4
nobind
# remote $server 443 tcp
remote $server 1194 udp
dev tun
dev-type tun
resolv-retry infinite
persist-key
persist-tun
remote-cert-tls server
ignore-unknown-option block-outside-dns
block-outside-dns
client-cert-not-required
# ns-cert-type server
# setenv opt tls-version-min 1.0 or-highest
# reneg-sec 604800
# sndbuf 0
# rcvbuf 0
auth-user-pass
# NOTE: LZO commands are pushed by the Access Server at connect time.
# NOTE: The below line doesn't disable LZO.
# comp-lzo no
verb 3
# setenv PUSH_PEER_INFO

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
&lt;/tls-crypt&gt;

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
