<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class BirthdayTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_birthday';
	}

	public static function getTableTitle ()
	{
		return 'Дни рождения';
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\StringField('NAME',array(
				'required' => true,
				'title' => 'Имя именинника'
			)),
			new Entity\StringField('NAME_SAY',array(
				'title' => 'Имя именинника для произношения с ударениями'
			)),
			new Entity\BooleanField('MALE',array(
				'required' => true,
				'default_value' => true,
				'title' => 'Флаг, мужчина'
			)),
			new Entity\IntegerField('DAY',array(
				'required' => true,
				'title' => 'Число рождения'
			)),
			new Entity\IntegerField('MONTH',array(
				'required' => true,
				'title' => 'Месяц рождения'
			)),
			new Entity\IntegerField('YEAR',array(
				'title' => 'Год рождения'
			)),
			new Entity\DateField('DATE',array(
				'title' => 'Дата рождения (год не важен)'
			))
		);
	}
}