<?php

use MSergeev\Packages\Kuzmahome\Lib;
use MSergeev\Core\Lib as CoreLib;

//Активация ночного режима
$WorkDayStart = Lib\Objects::getGlobal('propWorkDayStart');
$WorkDayStop = Lib\Objects::getGlobal('propWorkDayStop');
$WeekEndStart = Lib\Objects::getGlobal('propWeekEndStart');
$WeekEndStop = Lib\Objects::getGlobal('propWeekEndStop');
$NightModeActive = CoreLib\Options::getOptionInt('KUZMAHOME_IS_NIGHT_MODE');
$NightLevel = CoreLib\Options::getOptionInt('KUZMAHOME_NIGHT_LEVEL_MSG');
$DayLevel = CoreLib\Options::getOptionInt('KUZMAHOME_DAY_LEVEL_MSG');
$d = intval(date('d'));
$m = intval(date('m'));

if (
	(Lib\DateTime::isWeekDay() && Lib\DateTime::timeBetween($WorkDayStop, $WorkDayStart))
	|| (!Lib\DateTime::isWeekDay() && Lib\DateTime::timeBetween($WeekEndStop, $WeekEndStart)))
{
	if (!$NightModeActive)
	{
		CoreLib\Options::setOption('KUZMAHOME_IS_NIGHT_MODE',1);
		//CoreLib\Options::setOption('KUZMAHOME_MIN_MSG_LEVEL',$NightLevel);
		Lib\Objects::setGlobal('propMinMsgLevel',$NightLevel);
	}
}
elseif (
	(Lib\DateTime::isWeekDay() && Lib\DateTime::timeBetween($WorkDayStart, $WorkDayStop))
	|| (!Lib\DateTime::isWeekDay() && Lib\DateTime::timeBetween($WeekEndStart, $WeekEndStop)))
{
	if ($NightModeActive)
	{
		CoreLib\Options::setOption('KUZMAHOME_IS_NIGHT_MODE',0);
		//CoreLib\Options::setOption('KUZMAHOME_MIN_MSG_LEVEL',$DayLevel);
		Lib\Objects::setGlobal('propMinMsgLevel',$DayLevel);
	}
}
