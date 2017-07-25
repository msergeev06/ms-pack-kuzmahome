<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class SaidTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_said';
	}

	public static function getTableTitle ()
	{
		return 'Сказанное';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\DatetimeField('DATETIME',array(
				'required' => true,
				'title' => 'Дата и время произнесения фразы'
			)),
			new Entity\StringField('MESSAGE',array(
				'required' => true,
				'title' => 'Фраза'
			)),
			new Entity\IntegerField('ROOM_ID',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Комната'
			)),
			new Entity\IntegerField('MEMBER_ID',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'ID пользователя, если 0 - умный дом говорит'
			)),
			new Entity\IntegerField('LEVEL',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Уровень важности'
			)),
			new Entity\StringField('SOURCE',array(
				'title' => 'Источник'
			))
		);
	}
}