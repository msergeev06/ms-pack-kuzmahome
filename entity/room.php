<?php

namespace MSergeev\Packages\Kuzmahome\Entity;

use MSergeev\Packages\Kuzmahome\Tables;

class Room
{
	/**
	 * @var int ID комнаты
	 */
	private $id = null;

	/**
	 * @var string Название комнаты
	 */
	public $title = null;

	/**
	 * @var string Название комнаты на русском языке, со строчной буквы, отвечая на вопрос "где?"
	 */
	public $title2 = null;

	/**
	 * @var int Время последней активности в комнате time()
	 */
	public $latestActivity = null;

	/**
	 * @var string Время последней активности в комнате date('H:i')
	 */
	public $latestActivityTime = null;

	/**
	 * @var int Время в секундах, через которое считается что в комнате никого нет
	 */
	public $activityTimeout = null;

	/**
	 * @var bool Флаг, есть ли кто-то в комнате
	 */
	public $somebodyHere = false;

	/**
	 * @var float Температура в комнате
	 */
	public $temperature = null;

	/**
	 * @var float Температура, при которой считается, что в комнате жарко
	 */
	public $temperatureLevelHigh = null;

	/**
	 * @var float Температура, которая достигается в экономичном режиме работы
	 */
	public $temperatureLevelEconom = null;

	/**
	 * @var float Температура, которая достигается в обычном режиме работы
	 */
	public $temperatureLevelMain = null;

	/**
	 * @var float Температура, при которой считается, что в комнате холодно
	 */
	public $temperatureLevelLow = null;

	/**
	 * @var bool Флаг, обозначающий, что необходимо контроллировать температуру
	 */
	public $temperatureControl = false;

	/**
	 * @var float Влажность в комнате
	 */
	public $humidity = null;

	/**
	 * @var float Уровень влажности, при котором считается, что в комнате высокая влажность
	 */
	public $humidityLevelHigh = null;

	/**
	 * @var float Уровень влажности, при котором считается, что в комнате низкая влажность
	 */
	public $humidityLevelLow = null;

	/**
	 * @var bool Флаг, обозначающий, что необходимо следить за уровнем влажности в комнате
	 */
	public $humidityControl = false;

	/**
	 * @var float Уровень освещенности в комнате
	 */
	public $luminosity = null;

	/**
	 * @var float Уровень освещенности при котором считается, что в комнате темно
	 */
	public $luminosityLevelDark = null;

	/**
	 * @var float Уровень освещенности, при котором считается, что в комнате светло
	 */
	public $luminosityLevelLight = null;

	/**
	 * @var bool Флаг, сообщающий, что необходимо следить за уровнем освещености
	 */
	public $luminosityControl = false;

	/**
	 * @var int ID объекта колонки
	 */
	public $soundObjectID = null;

	/**
	 * @var bool Флаг, обозначающий, что комната принадлежит ребенку.
	 */
	public $kinderRoom = false;

	/**
	 * @var bool Флаг, обозначающий, что в комнате свят (не нужно шуметь и включать свет)
	 */
	public $sleepingTime = false;

	public function __construct ($roomID=null)
	{
		if (!is_null($roomID))
		{
			$arRes = array();
			$find = null;
			if (is_numeric($roomID))
			{
				$find = 'ID';
			}
			elseif (is_array($roomID))
			{
				$arRes = $roomID;
			}
			else
			{
				$find = 'NAME';
			}

			if (!is_array($roomID))
			{
				$arRes = Tables\RoomsTable::getList(
					array(
						'filter' => array(
							$find => $roomID
						),
						'limit' => 1
					)
				);
				if ($arRes && isset($arRes[0]))
				{
					$arRes = $arRes[0];
				}
			}

			$this->initFromArray($arRes);
		}
	}

	public function updateLatestActivity ()
	{
		$this->latestActivity = time();
		$this->latestActivityTime = date('H:i');
	}

	private function initFromArray($arRoom)
	{
		if (!empty($arRoom))
		{
			if (isset($arRoom['ID']))
			{
				$this->id = $arRoom['ID'];
			}
			if (isset($arRoom['TITLE']))
			{
				$this->title = $arRoom['TITLE'];
			}
			if (isset($arRoom['TITLE2']))
			{
				$this->title2 = $arRoom['TITLE2'];
			}
			if (isset($arRoom['LATEST_ACTIVITY']))
			{
				$this->$latestActivity = $arRoom['LATEST_ACTIVITY'];
			}
			if (isset($arRoom['LATEST_ACTIVITY_TIME']))
			{
				$this->latestActivityTime = $arRoom['LATEST_ACTIVITY_TIME'];
			}
			if (isset($arRoom['ACTIVITY_TIMEOUT']))
			{
				$this->activityTimeout = $arRoom['ACTIVITY_TIMEOUT'];
			}
			if (isset($arRoom['SOMEBODY_HERE']))
			{
				$this->somebodyHere = $arRoom['SOMEBODY_HERE'];
			}
			if (isset($arRoom['TEMPERATURE']))
			{
				$this->temperature = $arRoom['TEMPERATURE'];
			}
			if (isset($arRoom['TEMPERATURE_LEVEL_HIGH']))
			{
				$this->temperatureLevelHigh = $arRoom['TEMPERATURE_LEVEL_HIGH'];
			}
			if (isset($arRoom['TEMPERATURE_LEVEL_ECONOM']))
			{
				$this->temperatureLevelEconom = $arRoom['TEMPERATURE_LEVEL_ECONOM'];
			}
			if (isset($arRoom['TEMPERATURE_LEVEL_MAIN']))
			{
				$this->temperatureLevelMain = $arRoom['TEMPERATURE_LEVEL_MAIN'];
			}
		}
	}
}