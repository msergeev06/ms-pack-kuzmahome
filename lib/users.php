<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Entity\Query;
use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;

class Users extends CoreLib\Users
{
	public static function getAuthUserParams ($arParams = array())
	{
		global $USER;

		if ($USER->getParam('KUZMA_USER_ID') !== false)
		{
			return self::getUserParams($USER->getParam('KUZMA_USER_ID'),$arParams);
		}
		else
		{
			return array();
		}
	}

	public static function getUserParams($userID, $arParams = array())
	{
		$arReturn = array();
		$arSelect = array();
		$coreUserID = null;
		if (!empty($arParams))
		{
			$arMapArray = Tables\UsersTable::getMapArray();
			foreach ($arParams as $parameter)
			{
				$parameter = strtoupper($parameter);
				if ($parameter == 'ID')
				{
					continue;
				}
				if (isset($arMapArray[$parameter]))
				{
					$arSelect[] = $parameter;
				}
			}
		}

		$arList = array(
			'filter' => array(
				'ID' => $userID
			),
			'limit' => 1
		);
		if (!empty($arSelect))
		{
			if (!in_array('USER_ID',$arSelect))
			{
				$arSelect[] = 'USER_ID';
			}
			$arList['select'] = $arSelect;
		}

		$arRes = Tables\UsersTable::getList($arList);
		if ($arRes && isset($arRes[0]))
		{
			$arRes = $arRes[0];
		}
		if ($arRes)
		{
			$coreUserID = $arRes['USER_ID'];

			foreach ($arRes as $key=>$value)
			{
				$arReturn[$key] = $value;
			}
		}

		if (!is_null($coreUserID))
		{
			$arCore = parent::getUserParams($coreUserID, $arParams);
			if (!empty($arCore))
			{
				$arReturn = array_merge($arReturn,$arCore);
			}
		}

		return $arReturn;
	}

	public static function getUserObject ($userCoreID)
	{
		$query = new Query('select');
		$sqlHelp = new CoreLib\SqlHelper(Tables\UsersTable::getTableName());
		$sql = "SELECT\n\t"
			.$sqlHelp->wrapFieldQuotes('LINKED_OBJECT')."\nFROM\n\t"
			.$sqlHelp->wrapTableQuotes()."\nWHERE\n\t"
			.$sqlHelp->wrapFieldQuotes('USER_ID')." = ".intval($userCoreID)."\nLIMIT 1";
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		if ($ar_res = $res->fetch())
		{
			return $ar_res['LINKED_OBJECT'];
		}
		else
		{
			return false;
		}
	}

	public static function setUserParams ($userID, array $arParams)
	{
		$userID = intval($userID);
		if (isset($arParams) && !empty($arParams) && $userID > 0)
		{
			$arMapArray = Tables\UsersTable::getMapArray();
			$arUpdate = array();
			foreach ($arParams as $key=>$value)
			{
				if ($key == 'ID')
				{
					continue;
				}
				if (isset($arMapArray[$key]))
				{
					$arUpdate[$key] = $value;
				}
			}

			if (!empty($arUpdate))
			{
				Tables\UsersTable::update($userID,array("VALUES"=>$arUpdate));
			}

			$arRes = Tables\UsersTable::getList(
				array(
					'select' => array('USER_ID'),
					'filter' => array('ID'=>intval($userID)),
					'limit' => 1
				)
			);
			if ($arRes && isset($arRes[0]))
			{
				$arRes = $arRes[0];
			}
			if ($arRes)
			{
				parent::setUserParams($arRes['USER_ID'],$arParams);
			}
		}
	}
}