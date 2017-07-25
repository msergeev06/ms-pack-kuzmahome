<?php
define('NO_HTTP_AUTH',true);
include_once('/var/www/kuzmahome/config.php');
header('Content-Type: application/json');

if (isset($_POST['userID']) && isset($_POST['command']))
{
	sayUser($_POST['command'],intval($_POST['userID']),0);

	echo json_encode(array('result'=>'OK'));
}
