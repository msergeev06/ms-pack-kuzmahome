<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;

class Sound
{
	public static function playSound ($filename, $exclusive = 0, $priority = 0)
	{
		$ignoreSound = CoreLib\Options::getOptionInt('KUZMAHOME_IGNORE_SOUND');
		$soundsDir = self::getSoundDir();

		if (file_exists($soundsDir . $filename . '.mp3'))
			$filename = $soundsDir . $filename . '.mp3';
		elseif (file_exists($soundsDir . $filename))
			$filename = $soundsDir . $filename;

		if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnBeforePlaySound'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					$bStop = CoreLib\Events::executePackageEvent($arEvent,array($filename,$exclusive,$priority));
					if ($bStop===false)
						return;
				}
			}
		}

		if (!$ignoreSound)
		{
			if (file_exists($filename))
			{
				Linux::safe_exec('sudo mplayer ' . $filename, $exclusive, $priority);
				//exec('mplayer ' . $filename, $exclusive, $priority);
			}
		}

		if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnAfterPlaySound'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					CoreLib\Events::executePackageEvent($arEvent,array($filename,$exclusive,$priority));
				}
			}
		}

	}

	public static function getSoundDir ()
	{
		$dir = CoreLib\Config::getConfig('DIR_SOUNDS');
		if (!$dir)
		{
			$dir = CoreLib\Config::getConfig('DOCUMENT_ROOT').'sounds';
		}
		if (substr($dir, -1) == '/' || substr($dir, -1) == '\\')
		{
			$dir = substr($dir, 0, -1);
		}

		if (!file_exists($dir))
		{
			Files::createDir($dir);
		}

		return $dir.'/';
	}

	public static function getCachedVoiceDir ()
	{
		$dir = CoreLib\Config::getConfig('DIR_CACHE').'voice';
		if (!$dir)
		{
			$dir = CoreLib\Config::getConfig('DOCUMENT_ROOT').'cached/voice';
		}
		if (substr($dir, -1) == '/' || substr($dir, -1) == '\\')
		{
			$dir = substr($dir, 0, -1);
		}

		if (!file_exists($dir))
		{
			Files::createDir($dir);
		}

		return $dir.'/';
	}

	public static function getConstantVoiceDir ()
	{
		$dir = CoreLib\Config::getConfig('DIR_SAVED_VOICES');
		if (!$dir)
		{
			$dir = CoreLib\Config::getConfig('DOCUMENT_ROOT').'voices';
		}
		if (substr($dir, -1) == '/' || substr($dir, -1) == '\\')
		{
			$dir = substr($dir, 0, -1);
		}

		if (!file_exists($dir))
		{
			Files::createDir($dir);
		}

		return $dir.'/';
	}

	function playMedia($path, $host = 'localhost')
	{
		if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnBeforePlayMedia'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					$bStop = CoreLib\Events::executePackageEvent($arEvent,array($path,$host));
					if ($bStop===false)
						return;
				}
			}
		}

		$player = new Player();

		$out = $player->play($path, $host);

		if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnAfterPlayMedia'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					CoreLib\Events::executePackageEvent($arEvent,array($path,$host));
				}
			}
		}

	}

}