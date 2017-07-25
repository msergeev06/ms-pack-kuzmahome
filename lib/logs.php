<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;

class Logs
{
	public static function getLogsDir ()
	{
		$logsDir = CoreLib\Config::getConfig('DIR_LOGS');
		if (!$logsDir)
		{
			$logsDir = CoreLib\Config::getConfig('DOCUMENT_ROOT').'logs';
		}

		if (substr($logsDir, -1) == '/' || substr($logsDir, -1) == '\\')
		{
			$logsDir = substr($logsDir, 0, -1);
		}

		if (!file_exists($logsDir))
		{
			Files::createDir($logsDir);
			$data = "Deny From All";
			Files::saveFile($logsDir.'/.htaccess',$data);
		}

		return $logsDir.'/';
	}

	public static function debMes ($strMessage)
	{
		$logsDir = self::getLogsDir();
		$filename = $logsDir.'debmes-'.date("Ymd").".txt";
		$f1 = fopen ($filename, 'a');
		$tmp=explode(' ', microtime());
		fwrite($f1, date("H:i:s ").$tmp[0].' '.$strMessage."\n------------------\n");
		fclose ($f1);
		@chmod($filename, Files::getFileChmod());
	}

	public static function getCrontabLogFilename ($name)
	{
		return self::getLogsDir().'log-crontab_'.$name.'.txt';
	}

	public static function saveDailyLogs ()
	{
		//OnNewMinute
		$oldName = $filename = self::getCrontabLogFilename('on_new_minute');
		$filename = str_replace('.txt',date('-Y-m-d',strtotime('-1 day')),$filename).'.txt';
		rename($oldName,$filename);

		//OnNewHour
		$oldName = $filename = self::getCrontabLogFilename('on_new_hour');
		$filename = str_replace('.txt',date('-Y-m-d',strtotime('-1 day')),$filename).'.txt';
		rename($oldName,$filename);
	}
}