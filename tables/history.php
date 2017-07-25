<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class HistoryTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_history';
	}

	public static function getTableTitle ()
	{
		return 'История';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\DatetimeField('ADDED',array(
				'title' => 'Время добавления значения',
			)),
			new Entity\IntegerField('OBJECT_ID',array(
				'required' => true,
				'default_value' => 0,
				'link' => 'ms_kuzmahome_objects.ID',
				'title' => 'ID объекта'
			)),
			new Entity\IntegerField('METHOD_ID',array(
				'required' => true,
				'default_value' => 0,
				'link' => 'ms_kuzmahome_methods.ID',
				'title' => 'ID метода'
			)),
			new Entity\IntegerField('VALUE_ID',array(
				'required' => true,
				'default_value' => 0,
				'link' => 'ms_kuzmahome_property_values.ID',
				'title' => 'ID значения'
			)),
			new Entity\StringField('OLD_VALUE',array(
				'required' => true,
				'title' => 'Старое значение'
			)),
			new Entity\StringField('NEW_VALUE',array(
				'required' => true,
				'title' => 'Новое значение'
			)),
			new Entity\TextField('DETAILS',array(
				'title' => ''
			))
		);
	}
}