<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class MegadDevicesTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_megad_devices';
	}

	public static function getTableTitle ()
	{
		return 'Устройства MegaD';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_megad_properties' => 'DEVICE_ID'
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
			new Entity\StringField('MDID',array(
				'required' => true,
				'title' => 'MegaD-ID уникальное ID устройства в сети'
			)),
			new Entity\StringField('TYPE',array(
				'required' => true,
				'title' => 'Тип устройства'
			)),
			new Entity\IntegerField('CONNECTION_TYPE',array(
				'required' => true,
				'size' => 3,
				'default_value' => 0,
				'title' => 'Тип подключения'
			)),
			new Entity\IntegerField('PORT',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Порт'
			)),
			new Entity\StringField('IP',array(
				'required' => true,
				'title' => 'IP адрес'
			)),
			new Entity\StringField('PASSWORD',array(
				'required' => true,
				'default_value' => 'sec',
				'title' => 'Пароль доступа к устройству'
			)),
			new Entity\IntegerField('ADDRESS',array(
				'required' => true,
				'size' => 3,
				'default_value' => 0,
				'title' => ''
			)),
			new Entity\IntegerField('UPDATE_PERIOD',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Период обновления'
			)),
			new Entity\DatetimeField('NEXT_UPDATE',array(
				'title' => 'Время следующего обновления'
			)),
			new Entity\TextField('CONFIG',array(
				'title' => 'Конфиг'
			))
		);
	}
}