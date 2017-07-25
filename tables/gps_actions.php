<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class GpsActionsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_gps_actions';
	}

	public static function getTableTitle ()
	{
		return 'Действия';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\IntegerField('LOCATION_ID',array(
				'required' => true,
				'link' => 'ms_kuzmahome_gps_locations.ID',
				'title' => 'ID местоположения'
			)),
			new Entity\IntegerField('USER_ID',array(
				'required' => true,
				'link' => 'ms_kuzmahome_users.ID',
				'title' => 'ID пользователя'
			)),
			new Entity\IntegerField('ACTION_TYPE',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Тип действия'
			)),
			new Entity\IntegerField('SCRIPT_ID',array(
				'title' => 'ID скрипта'
			)),
			new Entity\TextField('CODE',array(
				'title' => 'Код действия'
			)),
			new Entity\TextField('LOG',array(
				'title' => 'Лог'
			)),
			new Entity\DatetimeField('EXECUTED',array(
				'title' => 'Время последнего срабатывания'
			))
		);
	}
}