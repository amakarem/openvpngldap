<?php

//$queryUrl = "https://35.231.50.232/RPC2";
//$queryUrl = "https://tn.university/RPC2";
$queryUrl = "https://ovpn.allheartcare.com/RPC2";
$queryData = "<?xml version='1.0'?>
	<methodCall>
		<methodName>GetSession</methodName>
		<params></params>
	</methodCall>";

$token = 'ahmed.e' . ':' . 'zzzzzzz';
$qr2 = "<?xml version='1.0'?>
	<methodCall>
		<methodName>GetUserlogin</methodName>
		<params></params>
	</methodCall>";
    

function curlput($queryUrl, $queryData, $token)
{
    $try = 0;
    start:
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        //CURLOPT_HEADER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        //CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => $token,
    ));
    $headers = array(
        'Content-Type: text/xml',
    );

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($curl);
    if (curl_errno($curl)) {
        if ($try < 3) {
            $try = $try + 1;
            curl_close($curl);
            goto start;
        } else {
            echo 'Error:' . curl_error($curl);
        }
    }
    curl_close($curl);
/*
$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
$header = substr($result, 0, $header_size);
$body = substr($result, $header_size);
print_r($header);
print_r($body);
 */
    //print_r($result);
    return $result;
}

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

$fullstring = curlput($queryUrl, $queryData, $token);

//echo PHP_EOL;
echo $fullstring;
//echo trim($fullstring);

$token = get_string_between($fullstring, '<string>', '</string>');

$token = 'SESSION_ID' . ':' . $token;
//echo PHP_EOL . $token . PHP_EOL;

$x = curlput($queryUrl, $qr2, $token);
print_r($x);
