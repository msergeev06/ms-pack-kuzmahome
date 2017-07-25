<?php

use MSergeev\Packages\Kuzmahome\Lib;
use MSergeev\Core\Lib as CoreLib;

if (Lib\DateTime::timeIs("20:45"))
{
	Lib\MoscowWeather::sayTomorrow(1);
}
if (Lib\DateTime::isWeekDay())
{
	if (Lib\DateTime::timeIs("08:05"))
	{
		Lib\MoscowWeather::sayToday(1);
	}
}
else
{
	if (Lib\DateTime::timeIs("09:05"))
	{
		Lib\MoscowWeather::sayToday(1);
	}
}

