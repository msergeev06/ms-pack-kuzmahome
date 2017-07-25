<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class ModbusDevicesTable extends DataManager
{
	public static function getTableName()
	{
		return 'ms_kuzmahome_modbus_devices';
	}

	public static function getTableTitle ()
	{
		return 'Устройства, работающие по протоколу Modbus';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название устройства'
			)),
			new Entity\StringField('HOST',array(
				'required' => true,
				'title' => 'Хост устройства'
			)),
			new Entity\StringField('PROTOCOL',array(
				'required' => true,
				'size' => 5,
				'default_value' => 'UDP',
				'title' => 'Протокол'
			)),
			new Entity\IntegerField('DEVICE_ID',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'ID устройства'
			)),
			new Entity\StringField('REQUEST_TYPE',array(
				'required' => true,
				'size' => 10,
				'title' => 'Тип запроса'
			)),
			new Entity\IntegerField('REQUEST_START',array(
				'required' => true,
				'default_value' => 0,
				'title' => ''
			)),
			new Entity\IntegerField('REQUEST_TOTAL',array(
				'required' => true,
				'default_value' => 0,
				'title' => ''
			)),
			new Entity\StringField('RESPONSE_CONVERT',array(
				'required' => true,
				'size' => 10,
				'title' => ''
			)),
			new Entity\TextField('DATA',array(
				'title' => ''
			)),
			new Entity\DatetimeField('CHECK_LATEST',array(
				'title' => 'Время последней проверки'
			)),
			new Entity\DatetimeField('CHECK_NEXT',array(
				'title' => 'Время следующей проверки'
			)),
			new Entity\IntegerField('POLLPERIOD',array(
				'required' => true,
				'default_value' => 0,
				'title' => ''
			)),
			new Entity\StringField('LINKED_OBJECT',array(
				'required' => true,
				'title' => 'Связанный объект'
			)),
			new Entity\StringField('LINKED_PROPERTY',array(
				'required' => true,
				'title' => 'Связанное свойство'
			)),
			new Entity\TextField('LOG',array(
				'title' => 'Лог'
			)),
			new Entity\IntegerField('PORT',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Номер порта'
			))
		);
	}
}