<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Entity\Query;
use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Core\Entity\DatetimeField;

class Say
{
	private static $sayTypes = array (
		'test',
		'error',
		'time',
		'weather',
		'tariff',
		'mode',
		'birthday',
		'telegram' => 'telegram.*'
	);

	public static function clearMessage ($message)
	{
		$message = str_replace ('\+', '_', $message);
		$message = str_replace ('+', '', $message);
		$message = str_replace ('_', '+', $message);

		return $message;
	}

	public static function sayGood ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $strSource = '')
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, $strSource, array ('EMOTION' => 'good'));
	}

	public static function sayEvil ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $strSource = '')
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, $strSource, array ('EMOTION' => 'evil'));
	}

	public static function sayJane ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $strSource = '')
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, $strSource, array ('SPEAKER' => 'jane'));
	}

	public static function sayOksana ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $strSource = '')
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, $strSource, array ('SPEAKER' => 'oksana'));
	}

	public static function sayAlyss ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $strSource = '')
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, $strSource, array ('SPEAKER' => 'alyss'));
	}

	public static function sayOmazh ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $strSource = '')
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, $strSource, array ('SPEAKER' => 'omazh'));
	}

	public static function sayZahar ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $strSource = '')
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, $strSource, array ('SPEAKER' => 'zahar'));
	}

	public static function sayEnglish ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $strSource = '')
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, $strSource, array ('LANG' => 'en-US', 'SPEED' => '0.1'));
	}


	public static function sayTest ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $arAddParam = array ())
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, 'test', $arAddParam);
	}

	public static function sayError ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $arAddParam = array ())
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, 'error', $arAddParam);
	}

	public static function sayTime ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $arAddParam = array ())
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, 'time', $arAddParam);
	}

	public static function sayWeather ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $arAddParam = array ())
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, 'weather', $arAddParam);
	}

	public static function sayTariff ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $arAddParam = array ())
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, 'tariff', $arAddParam);
	}

	public static function sayBirthday ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $arAddParam = array ())
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, 'birthday', $arAddParam);
	}

	public static function sayMode ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $arAddParam = array ())
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, 'mode', $arAddParam);
	}

	public static function sayUser ($strPhrase, $iMemberID = 0, $iLevel = 0, $iRoomID = 0, $arAddParam = array ())
	{
		self::say ($strPhrase, $iLevel, $iRoomID, $iMemberID, 'user_'.$iMemberID, $arAddParam);
	}

	public static function sayPattern ($strPhrase, $iRoomID = 0, $iMemberID = 0, $arAddParam = array ())
	{
		$optionSayLevel = CoreLib\Options::getOptionInt('PATTERN_SAY_LEVEL');

		self::say ($strPhrase, $optionSayLevel, $iRoomID, $iMemberID, 'pattern', $arAddParam);
	}



	public static function say ($strPhrase, $iLevel = 0, $iRoomID = 0, $iMemberID = 0, $strSource='', $arAddParam = array())
	{
		$strPhrase = self::parseSayMessage($strPhrase);

		$arRec = array(
			'MESSAGE' => $strPhrase,
			'DATETIME' => date('d.m.Y H:i:s'),
			'ROOM_ID' => intval($iRoomID),
			'MEMBER_ID' => intval($iMemberID),
			'SOURCE' => $strSource,
			'LEVEL' => intval($iLevel)
		);
		if (!empty($arAddParam))
		{
			$arRec = array_merge($arRec, $arAddParam);
		}
		$ignoreVoice = CoreLib\Options::getOptionInt('KUZMAHOME_IGNORE_VOICE');
		$speakSignal = CoreLib\Options::getOptionInt('KUZMAHOME_SPEAK_SIGNAL');
		$signalDingDong = CoreLib\Options::getOptionStr('KUZMAHOME_SIGNAL_DING_DONG');

		/*
		if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnBeforeSay'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					$bStop = CoreLib\Events::executePackageEvent($arEvent,array(&$arRec));
					if ($bStop===false)
						return;
				}
			}
		}
		*/
		$bStop = CoreLib\Events::runEvents('kuzmahome','OnBeforeSay',array(&$arRec));
		if ($bStop===false)
			return;

		$message = $arRec['MESSAGE'];
		$arRec['MESSAGE'] = $clearMessage = self::clearMessage($arRec['MESSAGE']);
		$res = Tables\SaidTable::add(array("VALUES"=>$arRec));
		if (!$res->getResult())
		{
			Logs::debMes('ERROR: Not save Said to db');
			return;
		}
		$arRec['MESSAGE'] = $message;

		$propMinMsgLevel = intval(Objects::getGlobal('propMinMsgLevel'));
		$propLastSayTime = intval(Objects::getGlobal('propLastSayTime'));
		//Проигрываем, если необходимо "дин-дон"
		//if ($arRec['LEVEL'] >= CoreLib\Options::getOptionInt('KUZMAHOME_MIN_MSG_LEVEL') && !$ignoreVoice)
		if ($arRec['LEVEL'] >= $propMinMsgLevel && !$ignoreVoice)
		{
			if ($speakSignal > 0)
			{
				$passed = time() - $propLastSayTime;
				if ($passed > 20)
				{
					Sound::playSound($signalDingDong, 1, $arRec['LEVEL']);
				}
			}
		}

		//CoreLib\Options::setOption('KUZMAHOME_LAST_SAY_TIME',time());
		//CoreLib\Options::setOption('KUZMAHOME_LAST_SAY_MESSAGE', $clearMessage);
		Objects::setGlobal('propLastSayTime',time());
		Objects::setGlobal('propLastSayMessage',$clearMessage);

		/*
		if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnSay'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					CoreLib\Events::executePackageEvent($arEvent,array($arRec, $ignoreVoice));
				}
			}
		}
		*/
		//Событие произношения текста
		CoreLib\Events::runEvents('kuzmahome','OnSay',array($arRec, $ignoreVoice));

		/*
		if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnAfterSay'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					CoreLib\Events::executePackageEvent($arEvent,array($arRec, $ignoreVoice));
				}
			}
		}
		*/
		CoreLib\Events::runEvents('kuzmahome','OnAfterSay',array($arRec, $ignoreVoice));

	}

	public static function showSaidMessages ($limit=15)
	{
		$arMessages = self::getSaidMessages($limit);
		//msDebug($arMessages);
		$html = '';
		$date = null;
		$bFirst = true;

		foreach ($arMessages as $arMess)
		{
			if ($bFirst)
			{
				$bFirst = false;
				if ($arMess['DATE']!=date('d.m.Y'))
				{
					$html .= '<b>'.$arMess['DATE'].'</b><br>';
				}
			}
			if (is_null($date))
			{
				$date = $arMess['DATE'];
			}
			elseif($date!=$arMess['DATE'])
			{
				$html .= '<b>'.$arMess['DATE'].'</b><br>';
				$date = $arMess['DATE'];
			}
			$color = 'black';
			$name = Objects::getGlobal('generalHomeName');
			if (!$name) $name = 'Кузя';
			if ($arMess['MEMBER_ID']!=0)
			{
				$arRes = Tables\UsersTable::getList(
					array(
						'select' => array('LINKED_OBJECT'),
						'filter' => array('USER_ID'=>$arMess['MEMBER_ID']),
						'limit' => 1
					)
				);
				if ($arRes && isset($arRes[0]))
				{
					$arRes = $arRes[0];
				}
				if (isset($arRes['LINKED_OBJECT']))
				{
					$color = Objects::getGlobal($arRes['LINKED_OBJECT'].'.propColor');
					$name = Objects::getGlobal($arRes['LINKED_OBJECT'].'.propFullName');
				}
				else
				{
					$color='#868686';
					$name = 'Гость';
				}
			}
			$html .= $arMess['TIME'].' <span style="color:';
			$html .= $color.';"><b>';
			$html .= $name.':</b> ';
			$html .= $arMess['MESSAGE'].'</span><hr style="padding:0;margin:2px;">';
		}



		return $html;
	}

	private static function getSaidMessages ($limit=15)
	{
		$stopMessTypes = unserialize(Objects::getGlobal('setupHistoryStopSayTypes'));
		$notIn = '';
		$bFirst = true;
		foreach ($stopMessTypes as $stop)
		{
			if ($bFirst)
			{
				$bFirst = false;
			}
			else
			{
				$notIn .= ',';
			}
			$notIn .= "'".$stop."'";
		}

		$arRes = array();
		$query = new Query('select');
		$sqlHelp = new CoreLib\SqlHelper(Tables\SaidTable::getTableName());
		$sql = "SELECT\n\t"
			.$sqlHelp->wrapFieldQuotes('ID').",\n\t"
			.$sqlHelp->wrapFieldQuotes('DATETIME').",\n\t"
			.$sqlHelp->wrapFieldQuotes('MESSAGE').",\n\t"
			.$sqlHelp->wrapFieldQuotes('MEMBER_ID').",\n\t"
			.$sqlHelp->wrapFieldQuotes('SOURCE')."\n"
			."FROM\n\t"
			.$sqlHelp->wrapTableQuotes()."\n"
			."WHERE\n\t"
			.$sqlHelp->wrapFieldQuotes('SOURCE')." NOT IN (".$notIn.")\n"
			."ORDER BY\n\t"
			.$sqlHelp->wrapFieldQuotes('DATETIME')." DESC\n"
			."LIMIT ".intval($limit);
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		while ($ar_res = $res->fetch())
		{
			$dateTime = DatetimeField::fetchDataModification($ar_res['DATETIME']);
			list($date,$time) = explode(' ',$dateTime);
			list($h,$i,$temp) = explode(':',$time);
			$time = $h.':'.$i;
			$arRes[$ar_res['ID']] = array(
				'ID' => $ar_res['ID'],
				'DATETIME' => $dateTime,
				'DATE' => $date,
				'TIME' => $time,
				'MESSAGE' => $ar_res['MESSAGE'],
				'MEMBER_ID' => $ar_res['MEMBER_ID'],
				'SOURCE' => $ar_res['SOURCE']
			);
		}

		if (!empty($arRes))
		{
			return $arRes;
		}

		return false;
	}


	private static function parseSayMessage ($strMessage)
	{
		if(preg_match_all('/#([a-z]*):([a-zA-Z-_.]*)#/',$strMessage,$m))
		{
			if (is_array($m[1]))
			{
				for ($i=0; $i<count($m[1]);$i++)
				{
					$strMessage = self::replaceMatch($strMessage,$m[0][$i],$m[1][$i],$m[2][$i]);
				}
			}
			else
			{
				$strMessage = self::replaceMatch($strMessage,$m[0],$m[1],$m[2]);
			}
		}

		return $strMessage;
	}

	private static function replaceMatch ($strMess,$match0,$match1,$match2)
	{
		if ($match1 == 'global')
		{
			$result = Objects::getGlobal($match2);
			if ($result === false)
			{
				$result = '';
			}
			$strMess = str_replace($match0,$result,$strMess);
		}
		elseif ($match1 == 'date')
		{
			$strMess = str_replace($match0,date($match2),$strMess);
		}
		else
		{
			$strMess = str_replace($match0,'',$strMess);
		}

		return $strMess;
	}
}