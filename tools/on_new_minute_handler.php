<?php
/*
//use MSergeev\Packages\Kuzmahome\Lib;
//$scriptsDir = \MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_ROOT').'scripts/';

Все перенесено в метод объекта System

//Вызываем метод Проверки ночного режима
callMethod('modeNight.checkMode');

//Вызываем метод Проверки Рассвета/Заката
callMethod('modeDarkness.checkMode');

//Подключаем скрипт Часов с боем
include($scriptsDir.'striking_clock.php');

//Подключаем скрипт Проверки необходимости сказать погоду
include($scriptsDir.'need_say_weather.php');

$nastyaAtHome = getGlobal('user_nastya.propAtHome');
$nastyaName = getGlobal('user_nastya.propFullName');

if ($nastyaAtHome)
{
	if (Lib\DateTime::timeIs(getGlobal('setupAirChildrenRoomTime')))
	{
		Lib\Say::say(getGlobal('setupAirChildrenRoomText'),2);
	}

	if (Lib\DateTime::timeIs(getGlobal('setupNastyaSleepTime1')))
	{
		Lib\Say::say (getGlobal('setupNastyaSleepText1'),2);
	}

	if (Lib\DateTime::timeIs(getGlobal('setupNastyaSleepTime2')))
	{
		Lib\Say::say (getGlobal('setupNastyaSleepText2'),2);
	}
}
*/
callMethod('System.OnNewMinute');

