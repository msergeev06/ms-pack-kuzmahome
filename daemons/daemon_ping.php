<?php
/* Общие команды для всех демонов */
include_once ("/var/www/kuzmahome/config.php");

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Lib;
set_time_limit(0);

$daemonName = 'ping';  /**<-- Обязательно изменить на уникальное имя демона*/

$bStopped = false;
Lib\Daemons::log($daemonName,'Daemon started');
/* end Общие команды для всех демонов*/

while (1)
{
	//Проверяем доступность хостов. По 1 за раз
	Lib\Http::checkAllHosts(1);

	/* Общие команды для всех демонов */
	if (Lib\Daemons::needBreak($daemonName))
	{
		$bStopped = true;
		break;
	}
	sleep(1);
	/* end Общие команды для всех демонов*/
}

/* Общие команды для всех демонов */
if (!$bStopped)
{
	Lib\Daemons::stopped($daemonName);
	Lib\Daemons::log($daemonName,"Daemon unexpected exit");
}
/* end Общие команды для всех демонов*/
