<?php
define('ROOTPATH',dirname(__FILE__));
require_once(ROOTPATH . "/conf/config.php");
require_once(ROOTPATH . "/lib/curlTools.class.php");
require_once(ROOTPATH . "/lib/checkBili.class.php");
require_once(ROOTPATH . '/log4php/Logger.php');
Logger::configure(ROOTPATH . '/log4php/config.xml');
$log = Logger::getLogger('checkMyPi');
$log->info("开始检查本机IP");
$ipXml = simplexml_load_file(ROOTPATH . '/conf/ip.xml');
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
    $ipXml->saveXML(ROOTPATH . "/conf/ip.xml");
    $log->info("IP地址变更啦");
}
$log->info("检查IP地址完毕");

