<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Core\Entity\Query;
use MSergeev\Packages\Kuzmahome\Tables;

class Birthday
{
	public static $sayBirthdayLevel = 2;

	public static function birthdayNextWeek ()
	{
		//strtotime('Mon last week')
		//strtotime('Sun last week')
		//strtotime('Mon this week')
		//strtotime('Sun this week')
		//strtotime('Mon next week')
		//strtotime('Sun next week')

		$w = intval(date('w'));
		if ($w == 0)
		{
			$startDate = strtotime('Mon this week');
			$endDate = strtotime('Sun this week');
		}
		else
		{
			$startDate = strtotime('Mon next week');
			$endDate = strtotime('Sun next week');
		}

		$startDay = intval(date('d',$startDate));
		$startMonth = intval(date('m',$startDate));
		$endDay = intval(date('d',$endDate));
		$endMonth = intval(date('m',$endDate));

		//26.06.2017 - 2.07.2017
		/*
		('MONTH' = 6 AND ('DAY'>=26 AND 'DAY' <= 30)) OR
		('MONTH' = 7 AND ('DAY'>=1 AND 'DAY' <= 2))
		*/
		$query = new Query('select');
		$sqlHelper = new CoreLib\SqlHelper(Tables\BirthdayTable::getTableName());
		$sql = "SELECT *\nFROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\nWHERE\n\t";

		if ($startMonth == $endMonth)
		{
			$sql .= $sqlHelper->wrapFieldQuotes('MONTH').' = '.$startMonth." AND\n\t("
				.$sqlHelper->wrapFieldQuotes('DAY')." >= ".$startDay." AND "
				.$sqlHelper->wrapFieldQuotes('DAY')." <= ".$endDay.")";
		}
		else
		{
			$t = intval(date('t',$startDate));
			$sql .= "("
				.$sqlHelper->wrapFieldQuotes('MONTH')." = ".$startMonth." AND ("
				.$sqlHelper->wrapFieldQuotes('DAY')." >= ".$startDay." AND "
				.$sqlHelper->wrapFieldQuotes('DAY')." <= ".$t.")) OR\n\t("
				.$sqlHelper->wrapFieldQuotes('MONTH')." = ".$endMonth." AND ("
				.$sqlHelper->wrapFieldQuotes('DAY')." >= 1 AND "
				.$sqlHelper->wrapFieldQuotes('DAY')." <= ".$endDay."))";
		}
		//msEchoVar($sql);
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		if ($res->getResult())
		{
			$arBirthdays = array();
			while ($ar_res = $res->fetch())
			{
				$arBirthdays[] = array(
					'ID'        => $ar_res['ID'],
					'NAME'      => $ar_res['NAME'],
					'NAME_SAY'  => $ar_res['NAME_SAY'],
					'MALE'      => $ar_res['MALE'],
					'DAY'       => $ar_res['DAY'],
					'MONTH'     => $ar_res['MONTH'],
					'YEAR'      => $ar_res['YEAR'],
					'DATE'      => $ar_res['DATE']
				);
			}

			if (!empty($arBirthdays))
			{
				$arPhrases = self::getPhrase('На сл+едующей нед+еле',$arBirthdays);
				Say::sayBirthday($arPhrases['START'],self::$sayBirthdayLevel);
				foreach ($arPhrases['BIRTHDAYS'] as $say)
				{
					Say::sayBirthday($say,self::$sayBirthdayLevel);
				}
			}

		}

	}

	public static function birthdayThisWeek ()
	{
		$w = intval(date('w'));
		if ($w == 0)
		{
			$startDate = strtotime('Mon last week');
			$endDate = strtotime('Sun last week');
		}
		else
		{
			$startDate = strtotime('Mon this week');
			$endDate = strtotime('Sun this week');
		}

		$startDay = intval(date('d',$startDate));
		$startMonth = intval(date('m',$startDate));
		$endDay = intval(date('d',$endDate));
		$endMonth = intval(date('m',$endDate));

		$query = new Query('select');
		$sqlHelper = new CoreLib\SqlHelper(Tables\BirthdayTable::getTableName());
		$sql = "SELECT *\nFROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\nWHERE\n\t";

		if ($startMonth == $endMonth)
		{
			$sql .= $sqlHelper->wrapFieldQuotes('MONTH').' = '.$startMonth." AND\n\t("
				.$sqlHelper->wrapFieldQuotes('DAY')." >= ".$startDay." AND "
				.$sqlHelper->wrapFieldQuotes('DAY')." <= ".$endDay.")";
		}
		else
		{
			$t = intval(date('t',$startDate));
			$sql .= "("
				.$sqlHelper->wrapFieldQuotes('MONTH')." = ".$startMonth." AND ("
				.$sqlHelper->wrapFieldQuotes('DAY')." >= ".$startDay." AND "
				.$sqlHelper->wrapFieldQuotes('DAY')." <= ".$t.")) OR\n\t("
				.$sqlHelper->wrapFieldQuotes('MONTH')." = ".$endMonth." AND ("
				.$sqlHelper->wrapFieldQuotes('DAY')." >= 1 AND "
				.$sqlHelper->wrapFieldQuotes('DAY')." <= ".$endDay."))";
		}
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		if ($res->getResult())
		{
			$arBirthdays = array();
			while ($ar_res = $res->fetch())
			{
				$arBirthdays[] = array(
					'ID'        => $ar_res['ID'],
					'NAME'      => $ar_res['NAME'],
					'NAME_SAY'  => $ar_res['NAME_SAY'],
					'MALE'      => $ar_res['MALE'],
					'DAY'       => $ar_res['DAY'],
					'MONTH'     => $ar_res['MONTH'],
					'YEAR'      => $ar_res['YEAR'],
					'DATE'      => $ar_res['DATE']
				);
			}

			if (!empty($arBirthdays))
			{
				$arPhrases = self::getPhrase('На +этой нед+еле',$arBirthdays);
				Say::sayBirthday($arPhrases['START'],self::$sayBirthdayLevel);
				foreach ($arPhrases['BIRTHDAYS'] as $say)
				{
					Say::sayBirthday($say,self::$sayBirthdayLevel);
				}
			}

		}
	}

	public static function birthdayTomorrow ()
	{
		$startDay = intval(date('d',strtotime('tomorrow')));
		$startMonth = intval(date('m',strtotime('tomorrow')));

		$query = new Query('select');
		$sqlHelper = new CoreLib\SqlHelper(Tables\BirthdayTable::getTableName());
		$sql = "SELECT *\nFROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\nWHERE\n\t"
			.$sqlHelper->wrapFieldQuotes('MONTH').' = '.$startMonth." AND\n\t"
			.$sqlHelper->wrapFieldQuotes('DAY')." = ".$startDay;
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		if ($res->getResult())
		{
			$arBirthdays = array();
			while ($ar_res = $res->fetch())
			{
				$arBirthdays[] = array(
					'ID'        => $ar_res['ID'],
					'NAME'      => $ar_res['NAME'],
					'NAME_SAY'  => $ar_res['NAME_SAY'],
					'MALE'      => $ar_res['MALE'],
					'DAY'       => $ar_res['DAY'],
					'MONTH'     => $ar_res['MONTH'],
					'YEAR'      => $ar_res['YEAR'],
					'DATE'      => $ar_res['DATE']
				);
			}

			if (!empty($arBirthdays))
			{
				$arPhrases = self::getPhrase('З+автра',$arBirthdays);
				Say::sayBirthday($arPhrases['START'],self::$sayBirthdayLevel);
				foreach ($arPhrases['BIRTHDAYS'] as $say)
				{
					Say::sayBirthday($say,self::$sayBirthdayLevel);
				}
			}

		}

	}

	public static function birthdayToday ()
	{
		$startDay = intval(date('d'));
		$startMonth = intval(date('m'));

		$query = new Query('select');
		$sqlHelper = new CoreLib\SqlHelper(Tables\BirthdayTable::getTableName());
		$sql = "SELECT *\nFROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\nWHERE\n\t"
			.$sqlHelper->wrapFieldQuotes('MONTH').' = '.$startMonth." AND\n\t"
			.$sqlHelper->wrapFieldQuotes('DAY')." = ".$startDay;
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		if ($res->getResult())
		{
			$arBirthdays = array();
			while ($ar_res = $res->fetch())
			{
				$arBirthdays[] = array(
					'ID'        => $ar_res['ID'],
					'NAME'      => $ar_res['NAME'],
					'NAME_SAY'  => $ar_res['NAME_SAY'],
					'MALE'      => $ar_res['MALE'],
					'DAY'       => $ar_res['DAY'],
					'MONTH'     => $ar_res['MONTH'],
					'YEAR'      => $ar_res['YEAR'],
					'DATE'      => $ar_res['DATE']
				);
			}

			if (!empty($arBirthdays))
			{
				$arPhrases = self::getPhrase('Сег+одня',$arBirthdays);
				Say::sayBirthday($arPhrases['START'],self::$sayBirthdayLevel);
				foreach ($arPhrases['BIRTHDAYS'] as $say)
				{
					Say::sayBirthday($say,self::$sayBirthdayLevel);
				}
			}

		}

	}

	private static function getPhrase ($when, $arBirthdays)
	{
		$arPhrases = array();
		$startPhrase = $when.' свой день рожд+ения ';
		if (count($arBirthdays)==1)
		{
			$startPhrase .= 'пр+азднует';
		}
		else
		{
			$startPhrase .= 'пр+азднуют';
		}

		$arPhrases['START'] = $startPhrase;

		foreach ($arBirthdays as $birth)
		{
			$say = '';
			if (isset($birth['NAME_SAY']))
			{
				$say .= $birth['NAME_SAY'];
			}
			else
			{
				$say .= $birth['NAME'];
			}
			if (!is_null($birth['YEAR']))
			{
				$say .= ', ';
				if ($birth['MALE']===true || $birth['MALE']=='Y')
				{
					$say .= 'кот+орому исполн+яется ';
				}
				else
				{
					$say .= 'кот+орой исполн+яется ';
				}
				$yearsOld = intval(date('Y'))-$birth['YEAR'];
				$say .= $yearsOld.' '.CoreLib\Tools::sayRusRight($yearsOld,'г+од','г+ода','л+ет');

			}
			$arPhrases['BIRTHDAYS'][] = $say;
		}

		return $arPhrases;
	}
}