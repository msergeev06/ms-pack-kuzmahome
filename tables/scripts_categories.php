<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class ScriptsCategoriesTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_scripts_categories';
	}

	public static function getTableTitle ()
	{
		return 'Группы скриптов';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_scripts' => 'CATEGORY_ID'
			)
		);
	}

	public static function getMap()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название группы'
			))
		);
	}
}