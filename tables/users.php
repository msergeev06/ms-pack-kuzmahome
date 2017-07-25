<?php

namespace MSergeev\Packages\Kuzmahome\Tables;

use MSergeev\Core\Lib\DataManager;
use MSergeev\Core\Entity;
use MSergeev\Core\Lib\TableHelper;

class UsersTable extends DataManager
{
	public static function getTableName ()
	{
		return 'ms_kuzmahome_users';
	}

	public static function getTableTitle ()
	{
		return 'Пользователи';
	}

	public static function getTableLinks ()
	{
		return array(
			'ID' => array(
				'ms_kuzmahome_gps_devices' => 'USER_ID',
				'ms_kuzmahome_gps_actions' => 'USER_ID'
			)
		);
	}

	public static function getMap ()
	{
		return array(
			TableHelper::primaryField(),
			new Entity\IntegerField('USER_ID',array(
				'required' => true,
				'link' => 'ms_core_users.ID',
				'title' => 'ID пользователя в таблице пользователей ядра'
			)),/*
			new Entity\StringField('USERNAME',array(
				'required' => true,
				'title' => 'Имя пользователя'
			)),
			new Entity\StringField('PASSWORD',array(
				'required' => true,
				'default_value' => 'b295244dccd5cb3fe9f0784d26f1a187'
			)),
			new Entity\StringField('NAME',array(
				'required' => true,
				'title' => 'Имя'
			)),
			new Entity\StringField('EMAIL',array(
				'title' => 'Email'
			)),*/
			new Entity\StringField('SKYPE',array(
				'title' => 'Skype'
			)),/*
			new Entity\StringField('MOBILE',array(
				'title' => 'Мобильный +79876543210'
			)),*/
			new Entity\IntegerField('AVATAR',array(
				'link' => 'ms_core_file.ID',
				'title' => 'Аватар. ID изображения'
			)),
			new Entity\BooleanField('IS_ADMIN',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг: является администратором'
			)),
			new Entity\BooleanField('IS_DEFAULT',array(
				'required' => true,
				'default_value' => false,
				'title' => 'Флаг: является пользователем по-умолчанию'
			)),
			new Entity\StringField('LINKED_OBJECT',array(
				'title' => 'Связанный объект'
			)),
			new Entity\StringField('HOST',array(
				'title' => 'Хост, с которого авторизуется данный пользователь'
			)),
			new Entity\StringField('COLOR',array(
				'title' => 'Код цвета'
			))
		);
	}
}