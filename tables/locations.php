<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class LocationsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_locations';
	}

	public static function getTableTitle ()
	{
		return 'Местоположения';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_objects' => 'ROOM_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Идентификатор расположения'
			)),
			new Entity\StringField('DESCRIPTION',array(
				'title' => 'Описание расположения'
			))
		);
	}
}