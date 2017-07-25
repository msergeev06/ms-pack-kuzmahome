<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class GpsDevicesTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_gps_devices';
	}

	public static function getTableTitle ()
	{
		return 'Устройства GPS';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_gps_log' => 'DEVICE_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название устройства'
			)),
			new Entity\IntegerField('USER_ID',array(
				'link' => 'ms_kuzmahome_users.ID',
				'title' => 'ID пользователя, чьё устройство'
			)),
			new Entity\StringField('LAT',array(
				'title' => 'Широта'
			)),
			new Entity\StringField('LON',array(
				'title' => 'Долгота'
			)),
			new Entity\DatetimeField('UPDATED',array(
				'title' => 'Время последнего обновления координат'
			)),
			new Entity\StringField('DEVICEID',array(
				'required' => true,
				'title' => 'ID устройства'
			)),
			new Entity\StringField('TOKEN',array(
				'title' => 'Токен устройства'
			)),
			new Entity\FloatField('HOME_DISTANCE',array(
				'title' => 'Расстояние до дома'
			))
		);
	}
}