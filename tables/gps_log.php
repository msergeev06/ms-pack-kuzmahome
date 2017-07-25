<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class GpsLogTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_gps_log';
	}

	public static function getTableTitle ()
	{
		return 'Логи GPS';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\DatetimeField('ADDED',array(
				'required' => true,
				'title' => 'Время добавления записи'
			)),
			new Entity\FloatField('LAT',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Широта'
			)),
			new Entity\FloatField('LON',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Долгота'
			)),
			new Entity\FloatField('ALT',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Высота над уровнем моря'
			)),
			new Entity\StringField('PROVIDER',array(
				'required' => true,
				'size' => 30,
				'title' => 'Поставщик GPS данных'
			)),
			new Entity\FloatField('SPEED',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Скорость'
			)),
			new Entity\IntegerField('BATTLEVEL',array(
				'required' => true,
				'size' => 3,
				'default_value' => 0,
				'title' => 'Уровень заряда батареи'
			)),
			new Entity\BooleanField('CHARGING',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг. Устройство заряжается'
			)),
			new Entity\StringField('DEVICEID',array(
				'required' => true,
				'title' => 'ID устройства'
			)),
			new Entity\IntegerField('DEVICE_ID',array(
				'link' => 'ms_kuzmahome_gps_devices.ID',
				'title' => 'ID устройства GPS'
			)),
			new Entity\IntegerField('LOCATION_ID',array(
				'link' => 'ms_kuzmahome_gps_locations.ID',
				'title' => 'ID местоположения'
			)),
			new Entity\FloatField('ACCURACY',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Точность GPS данных'
			))
		);
	}
}