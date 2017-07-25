<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;

class Handlers
{
	public static function isReboot()
	{
		$documentRoot = CoreLib\Config::getConfig('DOCUMENT_ROOT');
		if (file_exists($documentRoot.'reboot'))
		{
			return true;
		}

		return false;
	}

	public static function onNewMinuteHandler ()
	{
		if (!static::isReboot())
		{
			$filename = CoreLib\Config::getConfig('KUZMAHOME_ROOT').'tools/on_new_minute_handler.php';
			include ($filename);
		}
	}

	public static function onNewHourHandler ()
	{
		if (!static::isReboot())
		{
			$filename = CoreLib\Config::getConfig ('KUZMAHOME_ROOT').'tools/on_new_hour_handler.php';
			include ($filename);
		}
	}

	public static function onNewDayHandler ()
	{
		if (!static::isReboot())
		{
			$filename = CoreLib\Config::getConfig ('KUZMAHOME_ROOT').'tools/on_new_day_handler.php';
			include ($filename);
		}
	}

	public static function onNewMonthHandler ()
	{
		if (!static::isReboot())
		{
			$filename = CoreLib\Config::getConfig ('KUZMAHOME_ROOT').'tools/on_new_month_handler.php';
			include ($filename);
		}
	}

	public static function onNewYearHandler ()
	{
		if (!static::isReboot())
		{
			$filename = CoreLib\Config::getConfig ('KUZMAHOME_ROOT').'tools/on_new_year_handler.php';
			include ($filename);
		}
	}

	public static function onStartUpHandler ()
	{
		$filename = CoreLib\Config::getConfig('KUZMAHOME_ROOT').'tools/on_startup_handler.php';
		include ($filename);
	}
}