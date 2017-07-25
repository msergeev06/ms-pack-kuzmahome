<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class PropertiesTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_properties';
	}

	public static function getTableTitle ()
	{
		return 'Свойства объектов и классов';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_property_values' => 'PROPERTY_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название свойства'
			)),
			new Entity\TextField('DESCRIPTION',array(
				'title' => 'Описание свойства'
			)),
			new Entity\IntegerField('CLASS_ID',array(
				'link' => 'ms_kuzmahome_classes.ID',
				'title' => 'Привязка к классу'
			)),
			new Entity\IntegerField('OBJECT_ID',array(
				'link' => 'ms_kuzmahome_objects.ID',
				'title' => 'Привязка к объекту'
			)),
			new Entity\IntegerField('KEEP_HISTORY',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Хранить историю'
			)),
			new Entity\StringField('ONCHANGE',array(
				'title' => 'Название метода при изменении '
			)),
			new Entity\StringField('SYSTEM',array(
				'title' => 'Системный'
			))
		);
	}
}