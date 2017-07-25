<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class MethodsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_methods';
	}

	public static function getTableTitle ()
	{
		return 'Методы';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_history' => 'METHOD_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название метода'
			)),
			new Entity\TextField('DESCRIPTION',array(
				'title' => 'Описание метода'
			)),
			new Entity\IntegerField('OBJECT_ID',array(
				'link' => 'ms_kuzmahome_objects.ID',
				'title' => 'Привязка к объекту'
			)),
			new Entity\IntegerField('CLASS_ID',array(
				'link' => 'ms_kuzmahome_classes.ID',
				'title' => 'Привязка к классу'
			)),
			new Entity\TextField('CODE',array(
				'title' => 'Код метода'
			)),
			new Entity\IntegerField('CALL_PARENT',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Вызывать родительский метод'
			)),
			new Entity\IntegerField('SCRIPT_ID',array(
				'link' => 'ms_kuzmahome_scripts.ID',
				'title' => 'Привязка к скрипту'
			)),
			new Entity\DatetimeField('EXECUTED',array(
				'title' => 'Время последнего вызова'
			)),
			new Entity\StringField('EXECUTED_PARAMS',array(
				'title' => 'Параметры последнего вызова'
			))
		);
	}
}