<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Core\Entity\Query;

class Jobs
{
	public static function addScheduledJob($strTitle, $strCommands, $iTimestamp, $iExpire = 1800)
	{
		$arData = array(
			'TITLE' => $strTitle,
			'COMMANDS' => $strCommands,
			'RUNTIME' => date('d.m.Y H:i:s', intval($iTimestamp)),
			'EXPIRE' => date('d.m.Y H:i:s', (intval($iTimestamp) + intval($iExpire)))
		);

		$res = Tables\JobsTable::add(array("VALUES"=>$arData));
		if ($res->getResult())
		{
			return $res->getInsertId();
		}
		else
		{
			return false;
		}

	}

	public static function clearScheduledJob ($title)
	{
		//SQLExec("DELETE FROM jobs WHERE TITLE LIKE '" . DBSafe($title) . "'");
		$query = new Query('delete');
		$sqlHelper = new CoreLib\SqlHelper(Tables\JobsTable::getTableName());
		$sql = "DELETE FROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\nWHERE\n\t"
			.$sqlHelper->wrapFieldQuotes('TITLE')."\nLIKE '" . $title . "'";
		$query->setQueryBuildParts($sql);
		$res = $query->exec();

		return $res->getResult();
	}

	public static function deleteScheduledJob($id)
	{
		Tables\JobsTable::delete($id);
	}

	public static function clearTimeOut($title)
	{
		return static::clearScheduledJob($title);
	}

	public static function setTimeOut($title, $commands, $timeout)
	{
		static::clearTimeOut($title);

		return static::addScheduledJob($title, $commands, time() + $timeout);
	}

	public static function timeOutExists($title)
	{
		$arRes = Tables\JobsTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array(
					'TITLE' => $title,
					'PROCESSED' => false
				),
				'limit' => 1
			)
		);
		if ($arRes && isset($arRes[0]))
		{
			$arRes = $arRes[0];
		}
		if (isset($arRes['ID']))
		{
			return $arRes['ID'];
		}
		else
		{
			return false;
		}
	}

	public static function runScheduledJobs()
	{
		$query = new Query('delete');
		$sqlHelper = new CoreLib\SqlHelper(Tables\JobsTable::getTableName());
		$sql = "DELETE FROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\n"
			."WHERE\n\t"
			.$sqlHelper->wrapFieldQuotes('EXPIRE')." <= '" . date('Y-m-d H:i:s') . "'";
		$query->setQueryBuildParts($sql);
		$query->exec();

		$arRes = Tables\JobsTable::getList(
			array(
				'filter' => array(
					'PROCESSED' => false,
					'EXPIRED' => false,
					'<=RUNTIME' => date('d.m.Y H:i:s')
				)
			)
		);

		if (!$arRes)
		{
			return;
		}

		foreach ($arRes as $job)
		{
			$arUpdate = array(
				'PROCESSED' => true,
				'STARTED' => date('d.m.Y H:i:s')
			);
			Tables\JobsTable::update($job['ID'],array("VALUES"=>$arUpdate));
			$resEval = eval($job['COMMANDS']);
			if ($resEval === false)
			{
				Logs::debMes(sprintf('Error executing job %s (%s)', $job['TITLE'], $job['ID']) .' ('.__FILE__.')');
			}
		}
	}

}