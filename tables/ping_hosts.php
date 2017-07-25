<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class PingHostsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_ping_hosts';
	}

	public static function getTableTitle ()
	{
		return 'Список хостов для пинга';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Название'
			)),
			new Entity\StringField('HOSTNAME',array(
				'required' => true,
				'title' => 'HostName'
			)),
			new Entity\IntegerField('TYPE',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Тип'
			)),
			new Entity\BooleanField('ONLINE',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг статуса онлайн'
			)),
			new Entity\StringField('SEARCH_WORD',array(
				'title' => 'Поисковое слово'
			)),
			new Entity\DatetimeField('CHECK_LATEST',array(
				'title' => 'Время последней проверки'
			)),
			new Entity\DatetimeField('CHECK_NEXT',array(
				'title' => 'Время следующей проверки'
			)),
			new Entity\IntegerField('ONLINE_SCRIPT_ID',array(
				'title' => 'ID скрипта, выполняемого при переходе в онлайн'
			)),
			new Entity\TextField('ONLINE_CODE',array(
				'title' => 'Код, выполняемый при переходе в онлайн'
			)),
			new Entity\IntegerField('ONLINE_INTERVAL',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Интервал проверки, когда онлайн'
			)),
			new Entity\IntegerField('OFFLINE_SCRIPT_ID',array(
				'title' => 'ID скрипта, выполняемого при переходе в оффлайн'
			)),
			new Entity\TextField('OFFLINE_CODE',array(
				'title' => 'Код, выполняемый при переходе в оффлайн'
			)),
			new Entity\IntegerField('OFFLINE_INTERVAL',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Интервал проверки, когда оффлайн'
			)),
			new Entity\TextField('LOG',array(
				'title' => 'Лог'
			)),
			new Entity\StringField('LINKED_OBJECT',array(
				'title' => 'Связанный объект'
			)),
			new Entity\StringField('LINKED_PROPERTY',array(
				'title' => 'Связанное свойство'
			)),
			new Entity\IntegerField('COUNTER_CURRENT',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Текущее количество срабатываний'
			)),
			new Entity\IntegerField('COUNTER_REQUIRED',array(
				'required' => true,
				'default_value' => 0,
				'title' => 'Количество срабатываний, для переключения статуса'
			))
		);
	}
}