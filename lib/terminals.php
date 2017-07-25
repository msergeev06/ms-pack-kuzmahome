<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Packages\Kuzmahome\Entity\Terminal;
use MSergeev\Packages\Kuzmahome\Tables;

class Terminals
{
	private static $mainTerminal = 'main';

	public static function checkTerminal ()
	{
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		$arTerminal = Tables\TerminalsTable::getOne(
			array(
				'select' => array('ID','NAME','TITLE','HOST','USER_ID','LINKED_OBJECT'),
				'filter' => array('HOST'=>$remoteAddr)
			)
		);
		//msDebug($arTerminal);
		if ($arTerminal)
		{
			return $arTerminal;
		}
		if (isset($_REQUEST['terminal']) && strlen($_REQUEST['terminal'])>0)
		{
			$terminalName = $_REQUEST['terminal'];
			$arTerminal = Tables\TerminalsTable::getOne(
				array(
					'select' => array('ID','NAME','TITLE','HOST','USER_ID','LINKED_OBJECT'),
					'filter' => array('NAME'=>$terminalName)
				)
			);
			//msDebug($arTerminal);
			if ($arTerminal)
			{
				return $arTerminal;
			}
		}
		$arTerminal = Tables\TerminalsTable::getOne(
			array(
				'select' => array('ID','NAME','TITLE','HOST','USER_ID','LINKED_OBJECT'),
				'filter' => array('NAME'=>static::$mainTerminal)
			)
		);
		//msDebug($arTerminal);
		if ($arTerminal)
		{
			return $arTerminal;
		}

		return false;
	}

	public static function initTerminal ()
	{
		$arTerminal = self::checkTerminal();
		if ($arTerminal)
		{
			return new Terminal($arTerminal);
		}
	}

	public static function setOffline ($terminalID)
	{
		Tables\TerminalsTable::update($terminalID,array("VALUES"=>array('IS_ONLINE'=>false)));
	}
}