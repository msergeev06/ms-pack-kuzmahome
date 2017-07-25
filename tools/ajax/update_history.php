<?php
define('NO_HTTP_AUTH',true);
include_once('/var/www/kuzmahome/config.php');
use \MSergeev\Packages\Kuzmahome\Lib;
header('Content-Type: application/json');

$html = Lib\Say::showSaidMessages();
if ($html)
{
	$result='OK';
}
else
{
	$result='ERROR';
}

echo json_encode(array('result'=>$result,'html'=>$html));