<?php
include_once('/var/www/kuzmahome/config.php');
use MSergeev\Core\Lib as CoreLib;
global $DB;

$documentRoot = CoreLib\Config::getConfig('DOCUMENT_ROOT');
$dirBackupDb = CoreLib\Config::getConfig('DIR_BACKUP_DB');

//Устанавливаем флаг перезагрузки, чтобы автоматически запускаемые скрипты не исполнялись
if (!file_exists($documentRoot.'reboot'))
{
	$f1 = fopen($documentRoot.'reboot','w');
	fwrite($f1,date('Y-m-d H:i:s'));
	fclose($f1);
}

$issetDB=$DB->querySQL('SHOW DATABASES LIKE "'.CoreLib\Config::getConfig('DB_NAME').'"');
if (!$issetDB)
{
	//Если базы данных нет - создаем базу и восстанавливаем данные из бекапа
	$comm = $DB->getCreateDbCommand(CoreLib\Config::getConfig('DB_NAME'));
	exec($comm);
	$fileTime = null;
	$filePath = null;
	$dir=$dirBackupDb;
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (!is_dir($dir.$file) && $file != "." && $file != ".." && $file != ".htaccess")
				{
					if (strstr($file,CoreLib\Config::getConfig('DB_NAME'))!==false)
					{
						if (is_null($fileTime))
						{
							$fileTime = filemtime($dir.$file);
							$filePath = $dir.$file;
						}
						elseif (filemtime($dir.$file) > $fileTime)
						{
							$fileTime = filemtime($dir.$file);
							$filePath = $dir.$file;
						}
					}
				}
			}
			closedir($dh);
		}
	}
	if (!is_null($filePath))
	{
		$comm = $DB->getBackupCommand($filePath);
		exec($comm);
	}
}

//Убираем флаг перезагрузки
if (file_exists($documentRoot.'reboot'))
{
	unlink($documentRoot.'reboot');
}


if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnStartUp'))
{
	foreach ($arEvents as $sort=>$ar_events)
	{
		foreach ($ar_events as $arEvent)
		{
			CoreLib\Events::executePackageEvent($arEvent);
		}
	}
}
//CoreLib\Events::runEvents('kuzmahome','OnStartUp');
