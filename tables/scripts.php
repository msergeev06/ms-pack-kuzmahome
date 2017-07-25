<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;
use MSergeev\Packages\Kuzmahome\Lib\DateTime;

class ScriptsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_scripts';
	}

	public static function getTableTitle ()
	{
		return 'Скрипты';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_methods' => 'SCRIPT_ID',
				'ms_kuzmahome_web_wars' => 'SCRIPT_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название скрипта'
			)),
			new Entity\TextField('CODE',array(
				'required' => true,
				'title' => 'PHP код скрипта'
			)),
			new Entity\TextField('DESCRIPTION',array(
				'title' => 'Описание крипта'
			)),
			new Entity\IntegerField('CATEGORY_ID',array(
				'link' => 'ms_kuzmahome_scripts_categories.ID',
				'title' => 'Категория скрипта'
			)),
			new Entity\DatetimeField('EXECUTED',array(
				'title' => 'Время последнего запуска'
			)),
			new Entity\StringField('EXECUTED_PARAMS',array(
				'serialized' => true,
				'title' => 'Параметры последнего запуска'
			)),
			new Entity\BooleanField('RUN_PERIODICALLY',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг периодического запуска'
			)),
			new Entity\StringField('RUN_DAYS',array(
				'size' => 30,
				'title' => 'Запуск по дням'
			)),
			new Entity\StringField('RUN_TIME',array(
				'size' => 10,
				'title' => 'Запуск в определенное время'
			))
		);
	}
}