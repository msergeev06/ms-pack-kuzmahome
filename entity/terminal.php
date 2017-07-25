<?php

namespace MSergeev\Packages\Kuzmahome\Entity;

use MSergeev\Packages\Kuzmahome\Lib;
use MSergeev\Packages\Kuzmahome\Tables;

class Terminal
{
	private $id=null;
	private $name=null;
	private $title=null;
	private $host=null;
	private $userID=null;
	private $userObject=null;

	public function __construct ($arTerminal)
	{
		if (isset($arTerminal['ID']) && !is_null($arTerminal['ID']))
		{
			$this->id = intval($arTerminal['ID']);
		}
		if (isset($arTerminal['NAME']) && !is_null($arTerminal['NAME']))
		{
			$this->name = strtolower($arTerminal['NAME']);
		}
		if (isset($arTerminal['TITLE']) && !is_null($arTerminal['TITLE']))
		{
			$this->title = $arTerminal['TITLE'];
		}
		if (isset($arTerminal['HOST']) && !is_null($arTerminal['HOST']))
		{
			$this->host = $arTerminal['HOST'];
		}
		if (isset($arTerminal['USER_ID']) && !is_null($arTerminal['USER_ID']) && intval($arTerminal['USER_ID'])>0)
		{
			$this->userID = intval($arTerminal['USER_ID']);
		}
		if (isset($arTerminal['LINKED_OBJECT']) && !is_null($arTerminal['LINKED_OBJECT']))
		{
			$this->userObject = $arTerminal['LINKED_OBJECT'];
		}

		$this->updateActivity();
		$this->authUser();
	}

	public function getID()
	{
		return $this->id;
	}

	public function getName ()
	{
		return $this->name;
	}

	public function getTitle ()
	{
		return $this->title;
	}

	public function getHost ()
	{
		return $this->host;
	}

	public function getUserID ()
	{
		return $this->userID;
	}

	public function getUserObject ()
	{
		return $this->userObject;
	}

	private function authUser ()
	{
		global $USER;
		if (!is_null($this->userID) && $this->userID > 0 && $this->userID != 2 && $this->userID != $USER->getID())
		{
			$USER->logIn($this->userID);
		}
	}

	private function updateActivity ()
	{
		if (!is_null($this->id))
		{
			Tables\TerminalsTable::update($this->id,array("VALUES"=>array('LATEST_ACTIVITY'=>date('d.m.Y H:i:s'),'IS_ONLINE'=>true)));
			Lib\Jobs::setTimeOut('timer_terminal_activity_'.$this->id,'MSergeev\Packages\Kuzmahome\Lib\Terminals::setOffline('.$this->id.');',(5*60));
		}
	}
}