<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class ObjectsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_objects';
	}

	public static function getTableTitle ()
	{
		return 'Объекты';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_property_values' => 'OBJECT_ID',
				'ms_kuzmahome_properties' => 'OBJECT_ID',
				'ms_kuzmahome_methods' => 'OBJECT_ID',
				'ms_kuzmahome_history' => 'OBJECT_ID'
			)
		);
	}

	public static function getMap()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название объекта'
			)),
			new Entity\TextField('DESCRIPTION',array(
				'title' => 'Описание'
			)),
			new Entity\IntegerField('CLASS_ID',array(
				'required' => true,
				'link' => 'ms_kuzmahome_classes.ID',
				'title' => 'Привязка к классу'
			)),
			new Entity\IntegerField('ROOM_ID',array(
				'link' => 'ms_kuzmahome_locations.ID',
				'title' => 'ID комнаты'
			)),
			new Entity\IntegerField('KEEP_HISTORY',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Хранить историю, дней'
			)),
			new Entity\StringField('SYSTEM',array(
				'title' => 'System'
			))
		);
	}
}