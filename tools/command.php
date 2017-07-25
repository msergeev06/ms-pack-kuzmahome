<?php
define('NO_HTTP_AUTH',true);
include_once('/var/www/kuzmahome/config.php');
use \MSergeev\Packages\Kuzmahome\Lib;

if ($_SERVER['REMOTE_ADDR']=='192.168.0.24')
{
	sayUser ($_REQUEST['qry'],1,0);
}

//\MSergeev\Packages\Kuzmahome\Lib\Gps::parseGpsData($_REQUEST);
$strMessage = print_r($_REQUEST,true)."\nREMOTE_ADDR=".$_SERVER['REMOTE_ADDR'];

$logDir = Lib\Logs::getLogsDir();
$today_file = $logDir . 'log-command_' . date('Y-m-d') . '.txt';
$f1 = fopen ($today_file, 'a');
$tmp=explode(' ', microtime());
fwrite($f1, date("H:i:s ").$tmp[0].' '.$strMessage."\n------------------\n");
fclose ($f1);
@chmod($today_file, Lib\Files::getFileChmod());

