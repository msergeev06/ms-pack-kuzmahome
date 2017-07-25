<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class TerminalsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_terminals';
	}

	public static function getTableTitle ()
	{
		return 'Терминалы';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('NAME',array(
				'required' => true,
				'title' => 'Название терминала'
			)),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Заголовок терминала'
			)),
			new Entity\StringField('HOST',array(
				'required' => true,
				'title' => 'Хост адрес'
			)),
			new Entity\IntegerField('USER_ID',array(
				'title' => 'ID пользователя ядра'
			)),
			new Entity\StringField('LINKED_OBJECT',array(
				'title' => 'Объект пользователя'
			)),
			new Entity\BooleanField('IS_ONLINE',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг. Терминал online'
			)),
			new Entity\DatetimeField('LATEST_ACTIVITY',array(
				'title' => 'Время последней активности терминала'
			))
		);
	}
}