<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class PropertyValuesTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_property_values';
	}

	public static function getTableTitle ()
	{
		return 'Значения свойств';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_property_history' => 'VALUE_ID',
				'ms_kuzmahome_property_history_queue' => 'VALUE_ID',
				'ms_kuzmahome_history' => 'VALUE_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('PROPERTY_NAME',array(
				'required' => true,
				'title' => 'Название свойства'
			)),
			new Entity\IntegerField('PROPERTY_ID',array(
				'required' => true,
				'link' => 'ms_kuzmahome_properties.ID',
				'title' => 'ID свойства'
			)),
			new Entity\IntegerField('OBJECT_ID',array(
				'required' => true,
				'link' => 'ms_kuzmahome_objects.ID',
				'title' => 'ID объекта'
			)),
			new Entity\TextField('VALUE',array(
				'title' => 'Значение свойства'
			)),
			new Entity\DatetimeField('UPDATED',array(
				'title' => 'Время обновления значения свойства'
			)),
			new Entity\StringField('LINKED_MODULES',array(
				'title' => 'Связанный модуль'
			))
		);
	}
}