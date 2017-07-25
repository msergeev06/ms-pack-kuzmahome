<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class JobsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_jobs';
	}

	public static function getTableTitle ()
	{
		return 'Запланированные задания';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'ID запланированной задачи'
			)),
			new Entity\TextField('COMMANDS',array(
				'required' => true,
				'title' => 'PHP код'
			)),
			new Entity\DatetimeField('RUNTIME',array(
				'title' => 'Время запуска',
			)),
			new Entity\DatetimeField('EXPIRE',array(
				'title' => 'Время истечения задачи'
			)),
			new Entity\BooleanField('PROCESSED',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг исполнения задачи'
			)),
			new Entity\DatetimeField('STARTED',array(
				'title' => 'Время запуска задачи'
			)),
			new Entity\BooleanField('EXPIRED',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг истечения задачи'
			))
		);
	}
}