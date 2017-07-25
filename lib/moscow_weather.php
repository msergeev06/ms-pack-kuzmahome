<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;

class MoscowWeather
{
	private static $appid = 'ee04dde0551874138ce0843f64e1ed19';
	private static $cityID = 524901;

	private static $sayToday;
	private static $sayTomorrow;
	private static $parsedWeather;

	public static function getWeather ()
	{
		CoreLib\Loader::IncludePackage("owm");

		\MSergeev\Packages\Owm\Lib\Weather::getWeather(static::$cityID,static::$appid);

		self::parseWeather();
	}

	public static function sayToday ($level = 0)
	{
		self::parseWeather();

		$arSayToday = explode("|",static::$sayToday);
		foreach ($arSayToday as $say)
		{
			if (strlen($say)>0)
			{
				Say::sayWeather($say,$level);
			}
		}
	}

	public static function sayTomorrow ($level = 0)
	{
		self::parseWeather();

		$arSayTomorrow = explode("|",static::$sayTomorrow);

		foreach ($arSayTomorrow as $say)
		{
			if (strlen($say)>0)
			{
				Say::sayWeather($say,$level);
			}
		}
	}

	private static function parseWeather ()
	{
		//Используем ядро и модуль для MajorDoMo
		CoreLib\Loader::IncludePackage("mymajor");
		$arRes = \MSergeev\Packages\Mymajor\Lib\Weather::parseWeather(static::$cityID);

		if (isset($arRes[static::$cityID]["PARSED_WEATHER"]))
		{
			static::$parsedWeather = $arRes[static::$cityID]["PARSED_WEATHER"];
		}
		if (isset($arRes[static::$cityID]["SAY_TODAY"]))
		{
			static::$sayToday = $arRes[static::$cityID]["SAY_TODAY"];
		}
		if (isset($arRes[static::$cityID]["SAY_TOMORROW"]))
		{
			static::$sayTomorrow = $arRes[static::$cityID]["SAY_TOMORROW"];
		}
		if (isset($arRes[static::$cityID]["SUN_RISE"]))
		{
			//CoreLib\Options::setOption('KUZMAHOME_SUN_RISE',$arRes[static::$cityID]["SUN_RISE"]);
			Objects::setGlobal('propSunRiseTime',$arRes[static::$cityID]["SUN_RISE"]);
		}
		if (isset($arRes[static::$cityID]["SUN_SET"]))
		{
			//CoreLib\Options::setOption('KUZMAHOME_SUN_SET',$arRes[static::$cityID]["SUN_SET"]);
			Objects::setGlobal('propSunSetTime',$arRes[static::$cityID]["SUN_SET"]);
		}
		if (isset($arRes[static::$cityID]["DAY_TIME"]))
		{
			//CoreLib\Options::setOption('KUZMAHOME_SUN_DAY',$arRes[static::$cityID]["DAY_TIME"]);
			Objects::setGlobal('propSunDayTime',$arRes[static::$cityID]["DAY_TIME"]);
		}
	}
}