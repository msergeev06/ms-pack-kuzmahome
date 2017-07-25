<?php
include_once ("/var/www/kuzmahome/config.php");

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Lib;

set_time_limit(0);

$daemonName = 'main';

sleep(3);

$bStopped = false;

Lib\Daemons::log($daemonName,'Daemon started');

while (1)
{
	Lib\Objects::processDaemon();

	if (Lib\Daemons::needBreak($daemonName))
	{
		$bStopped = true;
		break;
	}

	sleep(1);
}

if (!$bStopped)
{
	Lib\Daemons::stopped($daemonName);
	Lib\Daemons::log($daemonName,"Daemon unexpected exit");
}