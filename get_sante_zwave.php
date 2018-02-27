<?php
$ID=$_GET['ID'];
$DECODE=$_GET['DECODE'];
$RETOUR=0;
$zwave_health_json = '/tmp/sante_zwave.json';
$bad_letters = array('\u00e0','\u00e2','\u00e4','\u00e7','\u00e8','\u00e9','\u00ea','\u00eb','\u00ee','\u00ef','\u00f4','\u00f6','\u00f9','\u00fb','\u00fc');
$good_letters   = array('&agrave;','&acirc;','&auml;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&icirc;','&iuml;','&ocirc;','&ouml;','&ugrave;','&ucirc;','&uuml;');
$text   = file_get_contents($zwave_health_json);
$output  = str_replace($bad_letters, $good_letters, $text);
file_put_contents($zwave_health_json, $output);
$json = file_get_contents($zwave_health_json);
$mod = json_decode($json, true);
$modules = $mod['result'];
$description = strtolower(utf8_decode($modules['devices'][$ID]['last_notification']['description']));

if ( $description == "dead" )
{
$RETOUR="1";
if ( $DECODE == True ) $RETOUR = "DEAD";
};

if ( $description == "sleep" )
{
$RETOUR="2";
if ( $DECODE == True ) $RETOUR = "SLEEP";
};

if ( $description == "timeout" )
{
$RETOUR="3";
if ( $DECODE == True ) $RETOUR = "TIMEOUT";
};
if ( $description == "nooperation" )
{
$RETOUR="4";
if ( $DECODE == True ) $RETOUR = "NO_OPERATION";
};
echo $RETOUR;
?>
