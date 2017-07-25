<?php

/*
Все перенесено в метод объекта System

use MSergeev\Packages\Kuzmahome\Lib;
$scriptsDir = \MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_ROOT').'scripts/';

//Устанавливаем время включения, для рассчета аптайма
\MSergeev\Core\Lib\Options::setOption('KUZMAHOME_STARTED_TIME',time());
//Проверяем демонов, которые должны быть запущены при старте
Lib\Daemons::checkDaemonsOnStartUp();

//Сообщаем о готовности
Lib\Say::say('Гот+ов к раб+оте',1);

//Подключаем скрипт Проверки тарифа электроэнергии
include($scriptsDir.'check_energo_tariff.php');
*/
$lastStartedTime = getGlobal('propStartedTime');
$nowTime = time();
$lostTime = $nowTime - $lastStartedTime;
if ($lostTime>3600)
{
	\MSergeev\Core\Lib\Events::runEvents('kuzmahome','OnNewHour');
}
if ($lostTime>86400)
{
	\MSergeev\Core\Lib\Events::runEvents('kuzmahome','OnNewDay');
}

callMethod('System.OnStartUp');
