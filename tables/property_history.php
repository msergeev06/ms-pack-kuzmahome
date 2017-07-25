<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class PropertyHistoryTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_property_history';
	}

	public static function getTableTitle ()
	{
		return 'Исторические значения свойств';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\IntegerField('VALUE_ID',array(
				'required' => true,
				'default_value' => 0,
				'link' => 'ms_kuzmahome_property_values.ID',
				'title' => 'ID исторического значения'
			)),
			new Entity\DatetimeField('ADDED',array(
				'title' => 'Время добавления значения'
			)),
			new Entity\StringField('VALUE',array(
				'required' => true,
				'title' => 'Историческое значение'
			))
		);
	}
}