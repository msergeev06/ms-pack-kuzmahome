<?php

use MSergeev\Packages\Kuzmahome\Lib;
use MSergeev\Core\Lib as CoreLib;

$logFilesExpire = intval(CoreLib\Config::getConfig('LOG_FILES_EXPIRE'));
if ($logFilesExpire==0) $logFilesExpire = 5;
$logFilesExpire = $logFilesExpire*24*60*60;
$backupFilesExpire = intval(CoreLib\Config::getConfig('BACKUP_FILES_EXPIRE'));
if ($backupFilesExpire==0) $backupFilesExpire = 10;
$backupFilesExpire = $backupFilesExpire*24*60*60;
$cachedFilesExpire = intval(CoreLib\Config::getConfig('CACHED_FILES_EXPIRE'));
if ($cachedFilesExpire==0) $cachedFilesExpire = 30;
$cachedFilesExpire = $cachedFilesExpire*24*60*60;

//Удаляем ненужные логи
$dir = CoreLib\Config::getConfig('DIR_LOGS');
if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (!is_dir($dir.$file) && $file != "." && $file != ".." && $file != ".htaccess")
			{
				if (filesize($dir.$file)==0)
				{
					Lib\Logs::debMes('Delete empty log file '.$dir.$file);
					@unlink($dir.$file);
				}
				elseif (filemtime($dir.$file) < (time() - $logFilesExpire))
				{
					Lib\Logs::debMes('Delete old log file '.$dir.$file);
					@unlink($dir.$file);
				}
			}
		}
		closedir($dh);
	}
}

//Удаляем ненужные бекапы
$dir = CoreLib\Config::getConfig('DIR_BACKUP_DB');
if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (!is_dir($dir.$file) && $file != "." && $file != ".." && $file != ".htaccess")
			{
				if (filesize($dir.$file)==0)
				{
					Lib\Logs::debMes('Delete empty backup file '.$dir.$file);
					@unlink($dir.$file);
				}
				elseif (filemtime($dir.$file) < (time() - $backupFilesExpire))
				{
					Lib\Logs::debMes('Delete old backup file '.$dir.$file);
					@unlink($dir.$file);
				}
			}
		}
		closedir($dh);
	}
}

//Удаляем ненужные фразы
$dir = CoreLib\Config::getConfig('DIR_CACHE').'voice/';
if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (!is_dir($dir.$file) && $file != "." && $file != ".." && $file != ".htaccess")
			{
				if (filesize($dir.$file)==0)
				{
					Lib\Logs::debMes('Delete empty voice file '.$dir.$file);
					@unlink($dir.$file);
				}
				elseif (filemtime($dir.$file) < (time() - $cachedFilesExpire))
				{
					Lib\Logs::debMes('Delete old voice file '.$dir.$file);
					@unlink($dir.$file);
				}
			}
		}
		closedir($dh);
	}
}


