<?php
define('NO_HTTP_AUTH',true);
include_once('/var/www/kuzmahome/config.php');
header('Content-Type: application/json');
global $USER;

if (isset($_POST['cookieName']) && isset($_POST['value']) && isset($_POST['userID']))
{
	if ($USER->setUserCookie($_POST['cookieName'],$_POST['value'],intval($_POST['userID'])))
	{
		$res = 'OK';
	}
	else
	{
		$res = 'ERROR';
	}

	echo json_encode(array('result'=>$res));
}
