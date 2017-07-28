<?php
//define('NO_HTTP_AUTH',true);
include_once('/var/www/kuzmahome/config.php');
use \MSergeev\Packages\Kuzmahome\Lib;

if (isset($_REQUEST['id']))
{
	$scriptID = $_REQUEST['id'];
	unset($_REQUEST['id']);
	Lib\Scripts::runScript($scriptID,$_REQUEST);
}

