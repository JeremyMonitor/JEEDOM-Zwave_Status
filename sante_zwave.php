<?php

$LOGIN = "VOTRE_LOGIN_ICI";
$MOT_DE_PASSE = "VOTRE_MOT_DE_PASSE";

$curl = curl_init();

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_COOKIEJAR, '/tmp/cookie.txt');
curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1/index.php?v=d');
$index = curl_exec($curl);
preg_match_all("/JEEDOM_AJAX_TOKEN='(.*?)'/", $index, $matches);
$JTOKEN = $matches[1][0];

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, 'jeedom_token='.$JTOKEN.'&action=login&username='.$LOGIN.'&password='.$MOT_DE_PASSE.'&storeConnection=0');
curl_setopt($curl, CURLOPT_COOKIEFILE, '/tmp/cookie.txt');
curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1/core/ajax/user.ajax.php');
$store = curl_exec($curl);

curl_setopt($curl, CURLOPT_POST, 0);
curl_setopt($curl, CURLOPT_COOKIEFILE, '/tmp/cookie.txt');
curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1/plugins/openzwave/core/php/jeeZwaveProxy.php?jeedom_token='.$JTOKEN.'&request=network%3Ftype%3Dinfo%26info%3DgetHealth');
$content = curl_exec($curl);

$zwave_health_json = '/tmp/sante_zwave.json';
file_put_contents('/tmp/sante_zwave.json', $content);
chmod('/tmp/sante_zwave.json',0777);
?>
