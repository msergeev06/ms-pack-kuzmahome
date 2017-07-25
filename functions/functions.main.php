<?php

use MSergeev\Packages\Kuzmahome\Lib;

if (!function_exists('say'))
{
	function say ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::say($phrase, $level, $roomID, $memberID, $source);
	}
}

//Разновидности функции say
if (!function_exists('sayGood'))
{
	function sayGood ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayGood($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayEvil'))
{
	function sayEvil ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayEvil($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayJane'))
{
	function sayJane ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayJane($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayOksana'))
{
	function sayOksana ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayOksana($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayAlyss'))
{
	function sayAlyss ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayAlyss($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayOmazh'))
{
	function sayOmazh ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayOmazh($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayZahar'))
{
	function sayZahar ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayZahar($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayEnglish'))
{
	function sayEnglish ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayEnglish($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayTest'))
{
	function sayTest ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayTest($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayError'))
{
	function sayError ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayError($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayTime'))
{
	function sayTime ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayTime($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayWeather'))
{
	function sayWeather ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayWeather($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayTariff'))
{
	function sayTariff ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayTariff($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayBirthday'))
{
	function sayBirthday ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayBirthday($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayMode'))
{
	function sayMode ($phrase, $level = 0, $roomID = 0, $memberID = 0, $source='')
	{
		Lib\Say::sayMode($phrase, $level, $roomID, $memberID, $source);
	}
}

if (!function_exists('sayUser'))
{
	function sayUser ($phrase, $memberID = 0, $level = 0, $roomID = 0, $arAddParam = array())
	{
		Lib\Say::sayUser($phrase, $memberID, $level, $roomID, $arAddParam);
	}
}

if (!function_exists('sayPattern'))
{
	function sayPattern ($strPhrase, $iRoomID = 0, $iMemberID = 0, $arAddParam = array ())
	{
		Lib\Say::sayPattern($strPhrase, $iRoomID, $iMemberID, $arAddParam);
	}
}
//---end Разновидности функции say


if (!function_exists('timeConvert'))
{
	function timeConvert ($tm)
	{
		return Lib\DateTime::timeConvert ($tm);
	}
}

if (!function_exists('playSound'))
{
	function playSound ($strSoundName, $i, $level = 0)
	{
		Lib\Sound::playSound ($strSoundName, $i, $level);
	}
}

if (!function_exists('DebMes'))
{
	function DebMes ($message)
	{
		Lib\Logs::debMes ($message);
	}
}

if (!function_exists('timeNow'))
{
	function timeNow ($tm = 0)
	{
		return Lib\DateTime::timeNow ($tm);
	}
}

if (!function_exists('isWeekEnd'))
{
	function isWeekEnd ()
	{
		return Lib\DateTime::isWeekEnd ();
	}
}

if (!function_exists('isWeekDay'))
{
	function isWeekDay ()
	{
		return Lib\DateTime::isWeekDay();
	}
}


if (!function_exists('timeIs'))
{
	function timeIs ($tm)
	{
		return Lib\DateTime::timeIs ($tm);
	}
}

if (!function_exists('timeBefore'))
{
	function timeBefore ($tm)
	{
		return Lib\DateTime::timeBefore ($tm);
	}
}

if (!function_exists('timeAfter'))
{
	function timeAfter ($tm)
	{
		return Lib\DateTime::timeAfter ($tm);
	}
}

if (!function_exists('timeBetween'))
{
	function timeBetween ($tm1, $tm2)
	{
		return Lib\DateTime::timeBetween ($tm1, $tm2);
	}
}

if (!function_exists('addScheduledJob'))
{
	function addScheduledJob ($strTitle, $strCommands, $iTimestamp, $iExpire = 1800)
	{
		return Lib\Jobs::addScheduledJob ($strTitle, $strCommands, $iTimestamp, $iExpire);
	}
}

if (!function_exists('clearScheduledJob'))
{
	function clearScheduledJob ($title)
	{
		Lib\Jobs::clearScheduledJob ($title);
	}
}

if (!function_exists('deleteScheduledJob'))
{
	function deleteScheduledJob ($id)
	{
		Lib\Jobs::deleteScheduledJob ($id);
	}
}

if (!function_exists('setTimeOut'))
{
	function setTimeOut ($title, $commands, $timeout)
	{
		return Lib\Jobs::setTimeOut ($title, $commands, $timeout);
	}
}

if (!function_exists('clearTimeOut'))
{
	function clearTimeOut ($title)
	{
		return Lib\Jobs::clearTimeOut ($title);
	}
}

if (!function_exists('timeOutExists'))
{
	function timeOutExists ($title)
	{
		return Lib\Jobs::timeOutExists ($title);
	}
}

if (!function_exists('runScheduledJobs'))
{
	function runScheduledJobs ()
	{
		Lib\Jobs::runScheduledJobs ();
	}
}

if (!function_exists('textToNumbers'))
{
	function textToNumbers ($text)
	{
		return Lib\MainFunction::textToNumbers ($text);
	}
}

if (!function_exists('recognizeTime'))
{
	function recognizeTime ($text, &$newText)
	{
		return Lib\DateTime::recognizeTime ($text, $newText);
	}
}

if (!function_exists('getRandomLine'))
{
	function getRandomLine ($filename)
	{
		return Lib\Files::getRandomLine ($filename);
	}
}

if (!function_exists('runScript'))
{
	function runScript ($id, $params = '')
	{
		Lib\Scripts::runScript ($id, $params);
	}
}

if (!function_exists('createDir'))
{
	function createDir ($path)
	{
		Lib\Files::createDir ($path);
	}
}

if (!function_exists('saveFile'))
{
	function saveFile ($filename, $data)
	{
		return Lib\Files::saveFile ($filename, $data);
	}
}

if (!function_exists('loadFile'))
{
	function loadFile ($filename)
	{
		return Lib\Files::loadFile ($filename);
	}
}

if (!function_exists('getURL'))
{
	function getURL ($url, $cache = 0, $username = '', $password = '')
	{
		return Lib\Http::getURL ($url, $cache, $username, $password);
	}
}

if (!function_exists('safe_exec'))
{
	function safe_exec ($command, $exclusive = 0, $priority = 0)
	{
		Lib\Linux::safe_exec ($command, $exclusive, $priority);
	}
}

if (!function_exists('execInBackground'))
{
	function execInBackground ($cmd)
	{
		Lib\Linux::execInBackground ($cmd);
	}
}

if (!function_exists('getFilesTree'))
{
	function getFilesTree ($destination, $sort = 'name')
	{
		return Lib\Files::getFilesTree ($destination, $sort);
	}
}

if (!function_exists('isOnline'))
{
	function isOnline ($host)
	{
		return Lib\Http::isOnline ($host);
	}
}

if (!function_exists('hexStringToArray'))
{
	function hexStringToArray ($buf)
	{
		return Lib\MainFunction::hexStringToArray ($buf);
	}
}

if (!function_exists('hexStringToString'))
{
	function hexStringToString ($buf)
	{
		return Lib\MainFunction::hexStringToString ($buf);
	}
}

if (!function_exists('binaryToString'))
{
	function binaryToString ($buf)
	{
		return Lib\MainFunction::binaryToString ($buf);
	}
}

if (!function_exists('return_memory_usage'))
{
	function return_memory_usage ()
	{
		return Lib\MainFunction::return_memory_usage ();
	}
}

if (!function_exists('win2utf'))
{
	function win2utf ($in)
	{
		return Lib\MainFunction::win2utf ($in);
	}
}

if (!function_exists('utf2win'))
{
	function utf2win ($in)
	{
		return Lib\MainFunction::utf2win ($in);
	}
}

if (!function_exists('checkEmail'))
{
	function checkEmail ($email)
	{
		return Lib\MainFunction::checkEmail ($email);
	}
}

if (!function_exists('sendMail'))
{
	function sendMail ($from, $to, $subj, $body, $attach = "")
	{
		return Lib\Mail::sendMail ($from, $to, $subj, $body, $attach);
	}
}

if (!function_exists('sendMail_HTML'))
{
	function sendMail_HTML ($from, $to, $subj, $body, $attach = "")
	{
		return Lib\Mail::sendMail_HTML ($from, $to, $subj, $body, $attach);
	}
}

if (!function_exists('genPassword'))
{
	function genPassword ($len = 5)
	{
		return Lib\MainFunction::genPassword ($len);
	}
}

if (!function_exists('ping'))
{
	function ping ($host)
	{
		return Lib\Http::ping ($host);
	}
}

if (!function_exists('transliterate'))
{
	function transliterate ($string)
	{
		return Lib\MainFunction::transliterate ($string);
	}
}

if (!function_exists('php_syntax_error'))
{
	function php_syntax_error ($code)
	{
		return Lib\MainFunction::php_syntax_error ($code);
	}
}

if (!function_exists('rs'))
{
	function rs ($id, $params = '')
	{
		Lib\Scripts::runScript ($id, $params);
	}
}

if (!function_exists('context_timeout'))
{
	function context_timeout ($context, $user)
	{
		Lib\Context::contextTimeout($context,$user);
	}
}

if (!function_exists('context_activate_ext'))
{
	function context_activate_ext ($id, $timeout = 0, $timeout_code = '', $timeout_context_id = 0)
	{
		Lib\Context::contextActivateExt($id, $timeout, $timeout_code, $timeout_context_id);
	}
}

if (!function_exists('context_clear'))
{
	function context_clear()
	{
		Lib\Context::contextClear();
	}
}

if (!function_exists('setVolume'))
{
	function setVolume($percent)
	{
		Lib\Linux::setVolume(intval($percent));
	}
}