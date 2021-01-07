<?php
$client = "someone";
$app = "global";

$AllowDeny = array(
    'allow' => array(
        '35.227.20.85', //old vpn
        '66.109.20.213', //vpn
    ),
);
$plazacore = __DIR__ . '/plaza';
if (!file_exists($plazacore)) {
    $plazacore = $plazacore . '.dat';
    if (!file_exists($plazacore)) {
        die('Require plaza');
    }
}
require_once $plazacore;

PlazaStart('helper');

AllowDeny($AllowDeny);

$filename = '/etc/openvpn/server/openvpn-status.log';
$log = FileToArray($filename);
unset($log[0]);
unset($log[1]);
$header = 0;
if (isset($_GET['html'])) {
    $output = '<table name="vpnstatus" id="vpnstatus">';
}
foreach ($log as $line) {
    if (strpos($line, 'ROUTING_TABLE,') === false && strpos($line, 'GLOBAL_STATS,Max') === false && strlen($line) >= 10) {
        $line = str_replace('HEADER,', '', $line);
        $line = str_replace('CLIENT_LIST,', '', $line);
        if (isset($output)) {
            $line = explode(',', $line);
            $html = '';
            foreach ($line as $value) {
                $html = $html . "<td>$value</td>";
            }
            if ($header == 0) {
                $header = 1;
                $output = $output . '<thead><tr>';
                $output = $output . str_replace('td>', 'th>', $html);
                $output = $output . '</tr></thead>';
                $output = $output . '<tbody>';
            } else {
                $output = $output . "<tr>$html</tr>";
            }
        } else {
            echo $line;
        }
    }
}
if ($header != 0 && isset($output)) {
    $output = $output . '</tbody>';
    $output = $output . '</table>';
    echo $output;
}
