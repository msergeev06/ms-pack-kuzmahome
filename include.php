<?php

// ---- SETUP ----
$packageName = "kuzmahome";
// ---------------

use \MSergeev\Core\Lib\Config;
use \MSergeev\Core\Lib\Loader;
use MSergeev\Packages\Kuzmahome\Entity;

$packageNameToUpper = strtoupper($packageName);
Config::addConfig($packageNameToUpper.'_ROOT',Config::getConfig('PACKAGES_ROOT').$packageName."/");
Config::addConfig($packageNameToUpper.'_PLUGINS_ROOT',Config::getConfig('PACKAGES_ROOT').$packageName."/plugins/");
Config::addConfig($packageNameToUpper.'_PUBLIC_ROOT',Config::getConfig('PUBLIC_ROOT').$packageName."/");
Config::addConfig($packageNameToUpper.'_TOOLS_ROOT',str_replace(Config::getConfig("SITE_ROOT"),"",Config::getConfig('PACKAGES_ROOT').$packageName."/tools/"));

//***** Other Classes *******
if (!class_exists('htmlMimeMail'))
{
	//include_once(Config::getConfig('PACKAGES_ROOT').$packageName.'/classes/htmlMimeMail.php');
}

//***** Tables ********
Loader::includeFiles(Config::getConfig($packageNameToUpper.'_ROOT')."tables/");

//***** Lib ********
Loader::includeFiles(Config::getConfig($packageNameToUpper.'_ROOT')."lib/");

//***** Entity ********
Loader::includeFiles(Config::getConfig($packageNameToUpper.'_ROOT')."entity/");

//***** Other Classes *******
//include_once(Config::getConfig('PACKAGES_ROOT').$packageName.'/classes/threads.php');

//***** Functions ********
include_once(Config::getConfig('PACKAGES_ROOT').$packageName.'/functions/functions.main.php');
include_once(Config::getConfig('PACKAGES_ROOT').$packageName.'/functions/functions.objects.php');

global $USER;
if ($USER->getID() != 2)
{
	$arRes = \MSergeev\Packages\Kuzmahome\Tables\UsersTable::getList(
		array(
			'select' => array('ID','LINKED_OBJECT'),
			'filter' => array('USER_ID'=>$USER->getID()),
			'limit' => 1
		)
	);
	if ($arRes && isset($arRes[0]))
	{
		$arRes = $arRes[0];
	}
	if ($arRes)
	{
		$USER->setParam('KUZMA_USER_ID',$arRes['ID']);
		$USER->setParam('LINKED_OBJECT',$arRes['LINKED_OBJECT']);
		$USER->setParam('propFullName',\MSergeev\Packages\Kuzmahome\Lib\Objects::getGlobal($arRes['LINKED_OBJECT'].'.propFullName'));
	}
}

$TERMINAL = \MSergeev\Packages\Kuzmahome\Lib\Terminals::initTerminal();
$GLOBALS['TERMINAL'] = $TERMINAL;
