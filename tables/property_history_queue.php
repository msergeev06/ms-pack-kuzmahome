<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class PropertyHistoryQueueTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_property_history_queue';
	}

	public static function getTableTitle ()
	{
		return 'Очередь исторических значений';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\IntegerField('VALUE_ID',array(
				'required' => true,
				'default_value' => 0,
				'link' => 'ms_kuzmahome_property_values.ID',
				'title' => 'ID значения'
			)),
			new Entity\TextField('VALUE',array(
				'required' => true,
				'title' => 'Значение'
			)),
			new Entity\TextField('OLD_VALUE',array(
				'required' => true,
				'title' => 'Старое значение'
			)),
			new Entity\IntegerField('KEEP_HISTORY',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Сколько хранить историю'
			)),
			new Entity\DatetimeField('ADDED',array(
				'title' => 'Время добавления значения'
			))
		);
	}
}