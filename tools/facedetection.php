<?php
define('NO_HTTP_AUTH',true);
include_once('/var/www/kuzmahome/config.php');
use \MSergeev\Packages\Kuzmahome\Lib;

//\MSergeev\Packages\Kuzmahome\Lib\Gps::parseGpsData($_REQUEST);
$strMessage = print_r($_REQUEST,true);

$logDir = Lib\Logs::getLogsDir();
$today_file = $logDir . 'log-facedetection_' . date('Y-m-d') . '.txt';
$f1 = fopen ($today_file, 'a');
$tmp=explode(' ', microtime());
fwrite($f1, date("H:i:s ").$tmp[0].' '.$strMessage."\n------------------\n");
fclose ($f1);
@chmod($today_file, Lib\Files::getFileChmod());

