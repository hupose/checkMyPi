<?php
require_once("conf/config.php");
require_once("lib/checkBili.class.php");

$checkBili = new checkBili();
$checkBili->checkNew();