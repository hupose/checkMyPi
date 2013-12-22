<?php

require_once("conf/config.php");
require_once("lib/curlTools.class.php");
require_once("lib/checkBili.class.php");


$ipXml = simplexml_load_file(ROOTPATH . 'conf/ip.xml');
$lastIP = $ipXml->item[0]->ip;
$lastIP = (string) $lastIP;
$lastIP = trim($lastIP);
$getIPUrl = 'http://members.3322.org/dyndns/getip';
$ip = curlTools::simpleCurl($getIPUrl);
$ip = trim($ip);
if($lastIP !== $ip){
    $checkBili = new checkBili();
    $checkBili->sendMail("IP地址有变更，新ip地址为：" . $ip);
    $ipXml->item[0]->ip = $ip;
    $ipXml->saveXML(ROOTPATH . "conf/ip.xml");
}

