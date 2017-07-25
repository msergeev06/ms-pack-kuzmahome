<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class MegadPropertiesTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_megad_properties';
	}

	public static function getTableTitle ()
	{
		return 'Параметры устройств MegaD';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\IntegerField('DEVICE_ID',array(
				'required' => true,
				'link' => 'ms_kuzmahome_megad_devices.ID',
				'title' => 'ID устройства MegaD'
			)),
			new Entity\IntegerField('TYPE',array(
				'required' => true,
				'size' => 3,
				'default_value' => 0,
				'title' => ''
			)),
			new Entity\IntegerField('NUM',array(
				'required' => true,
				'size' => 3,
				'default_value' => 0,
				'title' => ''
			)),
			new Entity\IntegerField('CURRENT_VALUE',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Текущее значение'
			)),
			new Entity\StringField('CURRENT_VALUE_STRING',array(
				'required' => true,
				'title' => ''
			)),
			new Entity\StringField('CURRENT_VALUE_STRING2',array(
				'required' => true,
				'title' => ''
			)),
			new Entity\IntegerField('COUNTER',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Счетчик'
			)),
			new Entity\StringField('LINKED_OBJECT',array(
				'required' => true,
				'title' => 'Связанный объект'
			)),
			new Entity\StringField('LINKED_PROPERTY',array(
				'required' => true,
				'title' => 'Связанное свойство'
			)),
			new Entity\StringField('LINKED_METHOD',array(
				'required' => true,
				'title' => 'Связанный метод'
			)),
			new Entity\StringField('LINKED_OBJECT2',array(
				'required' => true,
				'title' => 'Связанный объект2'
			)),
			new Entity\StringField('LINKED_PROPERTY2',array(
				'required' => true,
				'title' => 'Связанное свойство2'
			)),
			new Entity\StringField('LINKED_METHOD2',array(
				'required' => true,
				'title' => 'Связанный метод2'
			)),
			new Entity\StringField('ETH',array(
				'required' => true,
				'title' => ''
			)),
			new Entity\StringField('ECMD',array(
				'required' => true,
				'title' => ''
			)),
			new Entity\StringField('PWM',array(
				'required' => true,
				'title' => ''
			)),
			new Entity\StringField('MODE',array(
				'required' => true,
				'title' => ''
			)),
			new Entity\StringField('DEF',array(
				'required' => true,
				'title' => ''
			)),
			new Entity\StringField('MISC',array(
				'required' => true,
				'title' => ''
			)),
			new Entity\IntegerField('SKIP_DEFAULT',array(
				'required' => true,
				'default_value' => 0,
				'title' => ''
			)),
			new Entity\DatetimeField('UPDATED',array(
				'title' => 'Время обновления данных'
			))
		);
	}
}