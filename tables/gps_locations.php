<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class GpsLocationsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_gps_locations';
	}

	public static function getTableTitle ()
	{
		return 'Местоположения';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_gps_log' => 'LOCATION_ID',
				'ms_kuzmahome_gps_actions' => 'LOCATION_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название местоположения'
			)),
			new Entity\FloatField('LAT',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Широта'
			)),
			new Entity\FloatField('LON',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Долгота'
			)),
			new Entity\FloatField('RANGE',array(
				'required' => true,
				'default_value' => 500,
				'title' => 'Расстояние от местоположения до человека'
			)),
			new Entity\IntegerField('VIRTUAL_USER_ID',array(
				'required' => true,
				'default_value' => 0,
				'title' => ''
			)),
			new Entity\BooleanField('IS_HOME',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг, является ли данное местоположение домом'
			))
		);
	}
}