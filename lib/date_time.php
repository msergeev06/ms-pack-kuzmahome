<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;

class DateTime
{
	public static function timeConvert ($tm)
	{
		$tm = trim($tm);

		if (preg_match('/^(\d+):(\d+)$/', $tm, $m))
		{
			$hour     = $m[1];
			$minute   = $m[2];
			return mktime($hour, $minute, 0, (int)date('m'), (int)date('d'), (int)date('Y'));
		}
		elseif (preg_match('/^(\d+)$/', $tm, $m))
		{
			return $tm;
		}

		return false;
	}

	public static function timeNow ($tm = 0)
	{
		if (!$tm)
		{
			$tm = time();
		}
		$h = (int)date('G', $tm);
		$m = (int)date('i', $tm);

		switch($m)
		{
			case 1:
				$mText='одн+а';
				break;
			case 2:
				$mText='дв+е';
				break;
			case 21:
				$mText='дв+адцать одн+а';
				break;
			case 22:
				$mText='дв+адцать дв+е';
				break;
			case 31:
				$mText='тр+идцать одн+а';
				break;
			case 32:
				$mText='тр+идцать дв+е';
				break;
			case 41:
				$mText='с+орок одн+а';
				break;
			case 42:
				$mText='с+орок дв+е';
				break;
			case 51:
				$mText='пятьдес+ят одн+а';
				break;
			case 52:
				$mText='пятьдес+ят дв+е';
				break;
			default:
				$mText=$m.'';
				break;
		}

		return $h.' '.CoreLib\Tools::sayRusRight($h,'час','час+а','час+ов')
			.(($m>0)?' '.$mText.' '.CoreLib\Tools::sayRusRight($m,'мин+ута','мин+уты','мин+ут'):' р+овно');
	}

	public static function isWeekEnd ()
	{
		if (CoreLib\Loader::issetPackage('dates') && CoreLib\Loader::IncludePackage('dates'))
		{
			if (\MSergeev\Packages\Dates\Lib\WorkCalendar::isDayOff())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if (date('w') == 0 || date('w') == 6)
			{
				return true; // sunday, saturday
			}
			else
			{
				return false;
			}
		}
	}

	public static function isWeekDay()
	{
		if (CoreLib\Loader::issetPackage('dates') && CoreLib\Loader::IncludePackage('dates'))
		{
			if (!\MSergeev\Packages\Dates\Lib\WorkCalendar::isDayOff())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return !self::isWeekEnd();
		}
	}

	public static function timeIs ($tm)
	{
		if (date('H:i') == $tm)
			return true;

		return false;
	}

	public static function timeBefore($tm)
	{
		$trueTime = self::timeConvert($tm);

		if (time() <= $trueTime)
			return true;

		return false;
	}

	public static function timeAfter($tm)
	{
		$trueTime = self::timeConvert($tm);

		if (time() >= $trueTime)
			return true;

		return false;
	}

	public static function timeBetween($tm1, $tm2)
	{
		$trueTime1 = self::timeConvert($tm1);
		$trueTime2 = self::timeConvert($tm2);

		if ($trueTime1 > $trueTime2)
		{
			if ($trueTime2 < time())
			{
				$trueTime2 += 24 * 60 * 60;
			}
			else
			{
				$trueTime1 -= 24 * 60 * 60;
			}
		}

		if ((time() >= $trueTime1) && (time() <= $trueTime2))
			return true;

		return false;
	}

	public static function recognizeTime($text, &$newText)
	{
		$result   = 0;
		$found    = 0;
		$new_time = time();
		$text     = ($text);

		if (preg_match('/через (\d+) секунд.?/isu', $text, $m))
		{
			$new_time = time() + $m[1];
			$newText  = trim(str_replace($m[0], '', $text));
			$found    = 1;
		}
		elseif (preg_match('/через (\d+) минут.?/isu', $text, $m))
		{
			$new_time = time() + $m[1] * 60;
			$newText  = trim(str_replace($m[0], '', $text));
			$found    = 1;
		}
		elseif (preg_match('/через (\d+) час.?/isu', $text, $m))
		{
			$new_time = time() + $m[1] * 60 * 60;
			$newText  = trim(str_replace($m[0], '', $text));
			$found    = 1;
		}
		elseif (preg_match('/в (\d+):(\d+)/isu', $text, $m))
		{
			$new_time = mktime($m[1], $m[2], 0, (int)date('m'), (int)date('d'), (int)date('Y'));
			$newText  = trim(str_replace($m[0], '', $text));
			$found    = 1;
		}

		$newText = ($newText);

		if ($found)
		{
			$result = $new_time;
		}

		return $result;
	}


}