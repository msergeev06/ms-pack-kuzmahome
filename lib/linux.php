<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Entity\Query;
use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;

class Linux
{
	public static function setVolume ($percent)
	{
		if (intval($percent)<0 || intval($percent)>100)
			$percent = 100;

		exec('sudo amixer cset numid=1 -- '.$percent.'%');
	}

	public static function safe_exec ($command, $exclusive = 0, $priority = 0)
	{
		$arData = array(
			'COMMAND' => $command,
			'ADDED' => date('d.m.Y H:i:s'),
			'EXCLUSIVE' => CoreLib\Tools::validateBoolVal($exclusive),
			'PRIORITY' => intval($priority)
		);

		return Tables\SafeExecsTable::add(array("VALUES"=>$arData));
	}

	public static function deleteOldExecs ()
	{
		$query = new Query('delete');
		$sqlHelper = new CoreLib\SqlHelper(Tables\SafeExecsTable::getTableName());
		$sql = "DELETE FROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\n"
			."WHERE\n\t"
			.$sqlHelper->wrapFieldQuotes('ADDED')." < '" . date('Y-m-d H:i:s', time() - 180) . "'";
		$query->setQueryBuildParts($sql);
		$res = $query->exec();

		return $res->getResult();
	}

	public static function runExecs ($exclusive=false)
	{
		$arRes = Tables\SafeExecsTable::getList(
			array(
				'filter' => array('EXCLUSIVE'=>$exclusive),
				'order' => array('PRIORITY'=>'DESC','ID'=>'ASC'),
				'limit' => 5
			)
		);
		if ($arRes)
		{
			foreach($arRes as $arExec)
			{
				$query = new Query('delete');
				$sqlHelper = new CoreLib\SqlHelper(Tables\SafeExecsTable::getTableName());
				$sql = "DELETE FROM\n\t"
					.$sqlHelper->wrapTableQuotes()."\n"
					."WHERE\n\t"
					.$sqlHelper->wrapFieldQuotes('ID')." = '" . $arExec['ID'] . "'";
				$query->setQueryBuildParts($sql);
				$res = $query->exec();

				if ($res->getResult())
				{
					if ($exclusive)
					{
						Logs::debMes("Executing (exclusive): " . $arExec['COMMAND']);

						exec($arExec['COMMAND']);
					}
					else
					{
						Logs::debMes("Executing: " . $arExec['COMMAND']);

						self::execInBackground($arExec['COMMAND']);
					}
				}
			}
		}
	}

	public static function execInBackground($cmd)
	{
		exec($cmd . " > /dev/null &");
	}

}