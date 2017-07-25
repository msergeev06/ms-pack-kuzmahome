<?php
global $DB;
/*
Все перенесено в метод объекта System

use MSergeev\Packages\Kuzmahome\Lib;

$scriptsDir = \MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_ROOT').'scripts/';
$h = intval(date('H'));
$w = intval(date('w'));

//Подключаем скрипт Проверки тарифа электроэнергии
include($scriptsDir.'check_energo_tariff.php');

if ($h==19 || $h==20 || $h==21)
{
	//Проверяем необходимость передачи показаний электросчетчика
	Lib\Objects::callMethod('Energo3.checkNeedUpdate');
	//Проверяем необходимость передачи показаний счетчика холодной воды
	Lib\Objects::callMethod('WaterCold.checkNeedUpdate');
	//Проверяем необходимость передачи показаний счетчика горячей воды
	Lib\Objects::callMethod('WaterHot.checkNeedUpdate');
}

//Загружаем данные о погоде
Lib\MoscowWeather::getWeather();

if (Lib\DateTime::isWeekDay())
{ //В рабочий день в 7.00 и в 20.00
	if ($h == 10 || $h == 20)
	{
		Lib\Birthday::birthdayToday();
		Lib\Birthday::birthdayTomorrow();
	}
}
else
{ //В выходной в 9.00 и в 20.00
	if ($h == 10 || $h == 20)
	{
		Lib\Birthday::birthdayToday();
		Lib\Birthday::birthdayTomorrow();
	}
  //В воскресенье в 9.00 и в 21.00
	if ($w == 0 && ($h == 10 || $h == 21))
	{
		Lib\Birthday::birthdayNextWeek();
	}
}
*/
callMethod('System.OnNewHour');

//Делаем ежечасный бекап базы msergeev
exec($DB->getDumpCommand(\MSergeev\Core\Lib\Config::getConfig('DIR_BACKUP_DB'),'hourly'));