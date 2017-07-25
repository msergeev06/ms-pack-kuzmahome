<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class ClassesTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_classes';
	}

	public static function getTableTitle ()
	{
		return 'Классы';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_classes' => 'PARENT_ID',
				'ms_kuzmahome_methods' => 'CLASS_ID',
				'ms_kuzmahome_properties' => 'CLASS_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название класса'
			)),
			new Entity\IntegerField('PARENT_ID',array(
				'link' => 'ms_kuzmahome_classes.ID',
				'title' => 'Родительский класс'
			)),
			new Entity\TextField('SUB_LIST',array(
				'title' => 'Подклассы'
			)),
			new Entity\TextField('PARENT_LIST',array(
				'title' => 'Родительские классы'
			)),
			new Entity\BooleanField('NOLOG',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг, не вести лог'
			)),
			new Entity\TextField('DESCRIPTION',array(
				'title' => 'Описание класса'
			))
		);
	}
}


