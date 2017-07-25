<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class DaemonsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_daemons';
	}

	public static function getTableTitle ()
	{
		return 'Демоны';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('NAME',array(
				'required' => true,
				'title' => 'Название демона'
			)),
			new Entity\StringField('DESCRIPTION',array(
				'title' => 'Описание демона'
			)),
			new Entity\BooleanField('RUNNING',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг, запущен ли демон'
			)),
			new Entity\BooleanField('RUN',array(
				'required' => true,
				'default_value' => true,
				'title' => 'Флаг запуска/остановки'
			)),
			new Entity\BooleanField('RESTART',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг, обозначающий необходимость рестарта'
			)),
			new Entity\BooleanField('RUN_STARTUP',array(
				'required' => true,
				'default_value' => true,
				'title' => 'Флаг, запускать ли при старте'
			)),
			new Entity\IntegerField('PID',array(
				'title' => 'PID процесса'
			)),
			new Entity\DatetimeField('DATETIME_STARTED',array(
				'title' => 'Время старта демона'
			))
		);
	}

	public static function getAdditionalCreateSql()
	{
		return "DELIMITER //\n"
			."CREATE TRIGGER `before_update_".static::getTableName()."`\n"
			."BEFORE UPDATE ON `".static::getTableName()."` FOR EACH ROW\n"
			."BEGIN\n\t"
				."IF NEW.RUNNING LIKE 'Y' THEN\n\t\t"
					."SET NEW.DATETIME_STARTED = NOW();\n\t"
				."ELSEIF 1=1 THEN\n\t\t"
					."SET NEW.DATETIME_STARTED = NULL;\n\t"
				."END IF;\n"
			."END//\n"
			."DELIMITER ;";
	}

	public static function getAdditionalDeleteSql()
	{
		return "DROP TRIGGER IF EXISTS `before_update_ms_kuzmahome_daemons`;";
	}
}