<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;

class YandexTts
{
	public static function onSayHandler ($arRec, $ignoreVoice)
	{
		Logs::debMes('Yandex TTS: OnSayHandler ('.$arRec['SOURCE'].') ['.$arRec['LEVEL'].'] "'.Say::clearMessage($arRec['MESSAGE']).'"');
		/*
		$arRec = array(
			'MESSAGE' => $strPhrase,
			'DATETIME' => date('d.m.Y H:i:s'),
			'ROOM_ID' => intval($iRoomID),
			'MEMBER_ID' => intval($iMemberID),
			'SOURCE' => $strSource,
			'LEVEL' => intval($iLevel)
		);

		Doc: https://tech.yandex.ru/speechkit/cloud/doc/guide/concepts/tts-http-request-docpage/

		https://tts.voicetech.yandex.net/generate?

        key=<API‑ключ>

		& text=<текст>

		& format=<mp3|wav|opus>

		& [quality=<hi|lo>]

		& lang=<ru-RU|en-US|uk-UK|tr-TR>

		& speaker=<jane|oksana|alyss|omazh|zahar|ermil>

		& [speed=<скорость речи>]

		& [emotion=<good|neutral|evil>]
		*/
		if (!$ignoreVoice)
		{
			$arRec['MESSAGE'] = str_replace('\+','+',$arRec['MESSAGE']);
			$accessKey = CoreLib\Options::getOptionStr('KUZMAHOME_YANDEX_TTS_KEY');
			//$minMsgLevel = CoreLib\Options::getOptionInt('KUZMAHOME_MIN_MSG_LEVEL');
			$minMsgLevel = Objects::getGlobal('propMinMsgLevel');
			$yandexTtsSpeaker = CoreLib\Options::getOptionStr('KUZMAHOME_YANDEX_TTS_SPEAKER');
			$yandexTtsUrl = CoreLib\Options::getOptionStr('KUZMAHOME_YANDEX_TTS_URL');
			$cachedVoiceDir = Sound::getCachedVoiceDir();
			$constantVoiceDir = Sound::getConstantVoiceDir();

			if ($arRec['LEVEL'] >= intval($minMsgLevel) && $accessKey)
			{
				$param = array();
				$param['lang'] = 'ru-RU';
				$param['speaker'] = $yandexTtsSpeaker;
				$query = array(
					'format' => 'mp3',
					'lang' => 'ru-RU',
					'speaker' => $yandexTtsSpeaker,
					'key' => $accessKey,
					'text' => $arRec['MESSAGE']
				);
				if (isset($arRec['LANG']))
				{
					$query['lang'] = $arRec['LANG'];
					$param['lang'] = $arRec['LANG'];
				}
				if (isset($arRec['SPEAKER']))
				{
					$query['speaker'] = $arRec['SPEAKER'];
					$param['speaker'] = $arRec['SPEAKER'];
				}
				if (isset($arRec['EMOTION']))
				{
					$query['emotion'] = $arRec['EMOTION'];
					$param['emotion'] = $arRec['EMOTION'];
				}
				if (isset($arRec['speed']))
				{
					$query['speed'] = $arRec['SPEED'];
					$param['speed'] = $arRec['SPEED'];
				}

				$filename = md5($arRec['MESSAGE'].serialize($param)) . '_yandex.mp3';

				if (file_exists($constantVoiceDir.$filename))
				{
					$cachedFileName = $constantVoiceDir.$filename;
				}
				else
				{
					$cachedFileName = $cachedVoiceDir . $filename;
				}

				if (!file_exists($cachedFileName))
				{
					$qs = http_build_query($query);
					try
					{
						$contents = file_get_contents($yandexTtsUrl . $qs);
					}
					catch (\Exception $e)
					{
						Logs::debMes('yandextts ', get_class($e) . ', ' . $e->getMessage());
					}
					if (isset($contents))
					{
						Files::saveFile($cachedFileName, $contents);
						self::writeLog($arRec['MESSAGE'],$filename);
					}
				}
				else
				{
					@touch($cachedFileName);
				}

				if (file_exists($cachedFileName)) {
					Linux::safe_exec('sudo mplayer ' . $cachedFileName, 1, $arRec['LEVEL']);
				}
			}
		}
	}

	private static function writeLog ($message,$fileMp3)
	{
		$logsDir = Logs::getLogsDir();
		$filename = $logsDir.'yandex-tts-'.date("Ymd").".txt";
		$f1 = fopen ($filename, 'a');
		$tmp=explode(' ', microtime());
		fwrite($f1, date("H:i:s ").$tmp[0].' "'.$message.'" file: '.$fileMp3."\n------------------\n");
		fclose ($f1);
		@chmod($filename, Files::getFileChmod());
	}
}