<?php

use MSergeev\Core\Lib as CoreLib;

$packageName = 'kuzmahome';
CoreLib\Loader::IncludePackage($packageName);

use MSergeev\Packages\Kuzmahome\Lib as KuzmahomeLib;

//Подписываемся на события
CoreLib\Events::registerPackageDependences('kuzmahome','OnNewMinute',$packageName,'MSergeev\Packages\Kuzmahome\Lib\Handlers','onNewMinuteHandler');
CoreLib\Events::registerPackageDependences('kuzmahome','OnNewHour',$packageName,'MSergeev\Packages\Kuzmahome\Lib\Handlers','onNewHourHandler');
CoreLib\Events::registerPackageDependences('kuzmahome','OnNewDay',$packageName,'MSergeev\Packages\Kuzmahome\Lib\Handlers','onNewDayHandler');
CoreLib\Events::registerPackageDependences('kuzmahome','OnNewMonth',$packageName,'MSergeev\Packages\Kuzmahome\Lib\Handlers','onNewMonthHandler');
CoreLib\Events::registerPackageDependences('kuzmahome','OnNewYear',$packageName,'MSergeev\Packages\Kuzmahome\Lib\Handlers','onNewYearHandler');
CoreLib\Events::registerPackageDependences('kuzmahome','OnStartUp',$packageName,'MSergeev\Packages\Kuzmahome\Lib\Handlers','onStartUpHandler');

//Создаем таблицы в DB
//CoreLib\Installer::createPackageTables($packageName);

//Создаем символические ссылки на демонов
$daemonsLinkPath = KuzmahomeLib\Daemons::getDaemonsPath();
$daemonsPath = CoreLib\Config::getConfig(strtoupper($packageName).'_ROOT').'daemons/';
if (is_dir($daemonsPath))
{
	if ($dh = opendir($daemonsPath))
	{
		while (($file = @readdir($dh)) !== false)
		{
			if ($file != "." && $file != ".." && $file != ".daemon_blank.php")
			{
				if (!file_exists($daemonsLinkPath.$file))
				{
					symlink($daemonsPath.$file,$daemonsLinkPath.$file);
				}
			}
		}
		@closedir($dh);
	}
}

return true;