<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class SafeExecsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_safe_execs';
	}

	public static function getTableTitle ()
	{
		return 'Безопасный запуск функции exec';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\TextField('COMMAND',array(
				'required' => true,
				'title' => 'Команды shell'
			)),
			new Entity\DatetimeField('ADDED',array(
				'required' => true,
				'title' => 'Время добавления'
			)),
			new Entity\BooleanField('EXCLUSIVE',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Эксклюзивный запуск'
			)),
			new Entity\IntegerField('PRIORITY',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Приоритет'
			))
		);
	}
}