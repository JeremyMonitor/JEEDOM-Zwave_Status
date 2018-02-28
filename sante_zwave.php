<?php

/**
* Sante Zwave est un script permettant de vérifier l'état de santé d'un module
* Zwave dans Jeedom
*  
* Appel du script :
* 
* - Pour le mode mise � jour des �tat :
* /var/www/html/plugins/script/core/ressources/sante_zwave/sante_zwave.php
* 
* - Pour la consultation d'un �tat d'un ID (ex: ID 5)
* /var/www/html/plugins/script/core/ressources/sante_zwave/sante_zwave.php /ID=5
*  
* @param /ID=NUMERO_ID_MODULE
* @param /DECODE (Pour décoder le code état du module)
*
* Pour exécuter le script, dans un premier temps, créer une action script et ne pas indiquer de paramètres.
* Dans un second temps, créer des actions infos et indiquer un ID correspondant à votre module.
* 
* Pour toutes autres demandes, vous pouvez me contacter directement.
*
* TODO :
*   - Login et mot de passe en parametre
*   - Faire �voluer la m�thode de login et mot de passe avec le token API de jeedom 
* 
* 
* Auteur : Jeremy MONITOR - jeremy.monitor@mntr.fr
* 
* V1.0.0
* 
*/


$LOGIN = "";
$MOT_DE_PASSE = "";

if (! isset($DECODE)) { $DECODE="";}
    
    
if (isset($argv) && is_array($argv)) {
    $param = array();
    for ($x=1; $x<sizeof($argv);$x++) {
        
        if ( $argv[$x] == "/DECODE" ) { $DECODE = "True"; };
        
        
        $pattern = '#\/(.+)=(.+)#i';
        if (preg_match($pattern, $argv[$x])) {
            $key =  preg_replace($pattern, '$1', $argv[$x]);
            $val =  preg_replace($pattern, '$2', $argv[$x]);
            $_REQUEST[$key] = $val;
            $$key = $val;
           
        }
    }
}





if ( empty ($argv[1]) )
{
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
    $zwave_sante_json = '/tmp/sante_zwave.json';
    file_put_contents($zwave_sante_json, $content);
    chmod($zwave_sante_json,0777);
}


if  ( ! empty ($argv[1]) )
{
    
    $RETOUR=0;
    $zwave_sante_json = '/tmp/sante_zwave.json';
    $bad_letters = array('\u00e0','\u00e2','\u00e4','\u00e7','\u00e8','\u00e9','\u00ea','\u00eb','\u00ee','\u00ef','\u00f4','\u00f6','\u00f9','\u00fb','\u00fc');
    $good_letters   = array('&agrave;','&acirc;','&auml;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&icirc;','&iuml;','&ocirc;','&ouml;','&ugrave;','&ucirc;','&uuml;');
    $text   = file_get_contents($zwave_sante_json);
    $output  = str_replace($bad_letters, $good_letters, $text);
    file_put_contents($zwave_sante_json, $output);
    $json = file_get_contents($zwave_sante_json);
    $mod = json_decode($json, true);
    $modules = $mod['result'];
    $description = strtolower(utf8_decode($modules['devices'][$ID]['last_notification']['description']));

    if ( $description == "ok" )
    {
        $RETOUR="0";
        if ( $DECODE == "True" ) $RETOUR = "OK";
    };
    
    if ( $description == "dead" )
    {
        $RETOUR="1";
        if ( $DECODE == "True" ) $RETOUR = "DEAD";
    };
    
    if ( $description == "sleep" )
    {
        $RETOUR="2";
        if ( $DECODE == "True" ) $RETOUR = "SLEEP";
    };
    
    if ( $description == "timeout" )
    {
        $RETOUR="3";
        if ( $DECODE == "True" ) $RETOUR = "TIMEOUT";
    };
    if ( $description == "nooperation" )
    {
        $RETOUR="4";
        if ( $DECODE == "True" ) $RETOUR = "NO_OPERATION";
    };
    echo $RETOUR;
};




?>
