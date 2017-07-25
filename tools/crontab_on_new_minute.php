<?php
include_once('/var/www/kuzmahome/config.php');
use MSergeev\Core\Lib as CoreLib;

if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnNewMinute'))
{
	foreach ($arEvents as $sort=>$ar_events)
	{
		foreach ($ar_events as $arEvent)
		{
			CoreLib\Events::executePackageEvent($arEvent);
		}
	}
}
//CoreLib\Events::runEvents('kuzmahome','OnNewMinute');
