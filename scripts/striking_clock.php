<?php

use MSergeev\Packages\Kuzmahome\Lib;
use MSergeev\Core\Lib as CoreLib;

$WorkDayStart = Lib\Objects::getGlobal('propWorkDayStart');
$WorkDayStop = Lib\Objects::getGlobal('propWorkDayStop');
$WeekEndStart = Lib\Objects::getGlobal('propWeekEndStart');
$WeekEndStop = Lib\Objects::getGlobal('propWeekEndStop');

$h = (int)date ("G");
$m = (int)date ("i");
$d = intval(date('d'));
$mo = intval(date('m'));

if (intval($m)==0)
{
	Lib\Say::sayTime("Сейч+ас ".Lib\DateTime::timeNow(),0);
}
if (Lib\DateTime::isWeekDay()) {
	if (Lib\DateTime::timeBetween($WorkDayStart, $WorkDayStop))
	{
		if ($m=="00")
		{
			if ($h>12)
			{
				$h-=12;
			}
			Lib\Sound::playSound($h.'h.mp3',1);
		}
		if ($h == 7)
		{
			if ($m=="15")
			{
				Lib\Sound::playSound('15min.mp3',1);
			}
			if ($m=="45")
			{
				Lib\Sound::playSound('45min.mp3',1);
			}
		}
		if ($m=="30")
		{
			Lib\Say::sayTime("Сейч+ас ".Lib\DateTime::timeNow(),1);
			Lib\Sound::playSound('30min.mp3',1);
		}
	}
}
else
{
	if (Lib\DateTime::timeBetween($WeekEndStart, $WeekEndStop)
		|| (Lib\DateTime::timeBetween($WeekEndStart, '23:10') && $d==31 && $mo==12))
	{
		if ($m=="00")
		{
			if ($d==31 && $mo==12)
			{
				$text = 'До н+ового г+ода ост+алось ';
				switch ($h)
				{
					case 9:
						$text .= 'пятн+адцать час+ов';
						break;
					case 10:
						$text .= 'чет+ырнадцать час+ов';
						break;
					case 11:
						$text .= 'трин+адцать час+ов';
						break;
					case 12:
						$text .= 'двен+адцать час+ов';
						break;
					case 13:
						$text .= 'од+инадцать час+ов';
						break;
					case 14:
						$text .= 'д+есять час+ов';
						break;
					case 15:
						$text .= 'д+евять час+ов';
						break;
					case 16:
						$text .= 'в+осемь час+ов';
						break;
					case 17:
						$text .= 'семь час+ов';
						break;
					case 18:
						$text .= 'шесть час+ов';
						break;
					case 19:
						$text .= 'пять час+ов';
						break;
					case 20:
						$text .= 'чет+ыре час+а';
						break;
					case 21:
						$text .= 'три час+а';
						break;
					case 22:
						$text .= 'два час+а';
						break;
					case 23:
						$text .= 'од+ин час';
						break;
				}
				Lib\Say::sayTime($text,2);
			}
			else
			{
				Lib\Say::sayTime("Сейч+ас ".Lib\DateTime::timeNow(),0);
			}
			if ($h>12)
			{
				$h-=12;
			}
			Lib\Sound::playSound($h.'h.mp3',1);
		}
		if ($m=="30")
		{
			Lib\Say::sayTime("Сейч+ас ".Lib\DateTime::timeNow(),1);
			Lib\Sound::playSound('30min.mp3',1);
		}
	}
}

