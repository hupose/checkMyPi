<?php
define('FILEPATH',dirname(__FILE__));
require_once(FILEPATH . "/conf/config.php");
require_once(FILEPATH . "/lib/checkBili.class.php");

$checkBili = new checkBili();
$checkBili->checkNew();