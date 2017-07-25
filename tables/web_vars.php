<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class WebVarsTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_web_vars';
	}

	public static function getTableTitle ()
	{
		return 'Веб-переменные';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('TITLE',array(
				'required' => true,
				'title' => 'Идентификатор переменной'
			)),
			new Entity\StringField('HOSTNAME',array(
				'required' => true,
				'title' => 'Ссылка на страницу'
			)),
			new Entity\StringField('ENCODING',array(
				'required' => true,
				'default_value' => 'utf-8',
				'title' => 'Кодировка страницы'
			)),
			new Entity\StringField('SEARCH_PATTERN',array(
				'required' => true,
				'default_value' => '(.*)',
				'title' => 'Шаблон поиска'
			)),
			new Entity\TextField('LATEST_VALUE',array(
				'title' => 'Последнее значение'
			)),
			new Entity\IntegerField('CHECK_INTERVAL',array(
				'required' => true,
				'default_value' => 60,
				'title' => 'Интервал проверки'
			)),
			new Entity\DatetimeField('CHECK_LATEST',array(
				'title' => 'Время последней проверки'
			)),
			new Entity\DatetimeField('CHECK_NEXT',array(
				'title' => 'Время следующей проверки'
			)),
			new Entity\IntegerField('SCRIPT_ID',array(
				'link' => 'ms_kuzmahome_scripts.ID',
				'title' => 'ID скрипта при обновлении переменной'
			)),
			new Entity\StringField('LINKED_OBJECT',array(
				'title' => 'Связанный объект'
			)),
			new Entity\StringField('LINKED_PROPERTY',array(
				'title' => 'Связанное свойство'
			)),
			new Entity\TextField('CODE',array(
				'title' => 'Код, исполняемый при получении значения'
			)),
			new Entity\BooleanField('NEED_AUTH',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг необходимости авторизации'
			)),
			new Entity\StringField('AUTH_USERNAME',array(
				'size' => 100,
				'title' => 'Имя пользователя для авторизации'
			)),
			new Entity\StringField('AUTH_PASSWORD',array(
				'size' => 100,
				'title' => 'Пароль пользователя для авторизации'
			))
		);
	}
}