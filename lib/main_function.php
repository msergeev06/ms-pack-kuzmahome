<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Core\Lib as CoreLib;
use MSergeev\Core\Entity\Query;

class MainFunction
{
	/**
	 * @deprecated
	 * @see MSergeev\Packages\Kuzmahome\Lib\Logs::debMes
	 * @param $strMessage
	 */
	public static function debMes ($strMessage)
	{
		Logs::debMes($strMessage);
	}

	public static function textToNumbers($text)
	{
		return ($text);
	}

	public static function hexStringToArray ($buf)
	{
		$res       = array();
		$bufLength = strlen($buf) - 1;

		for ($i = 0; $i < $bufLength; $i += 2)
		{
			$res[] = (hexdec($buf[$i] . $buf[$i + 1]));
		}

		return $res;
	}

	public static function hexStringToString($buf)
	{
		$res       = '';
		$bufLength = strlen($buf) - 1;
		for ($i = 0; $i < $bufLength; $i += 2)
		{
			$res .= chr(hexdec($buf[$i] . $buf[$i + 1]));
		}

		return $res;
	}

	public static function binaryToString($buf)
	{
		$res = '';
		$bufLength = strlen($buf);

		for ($i = 0; $i < $bufLength; $i++)
		{
			$num = dechex(ord($buf[$i]));
			if (strlen($num) == 1)
			{
				$num = '0' . $num;
			}

			$res .= $num;
		}

		return $res;
	}

	public static function return_memory_usage()
	{
		$size=memory_get_usage(true);
		$unit=array('b','kb','mb','gb','tb','pb');
		$i=(int)floor(log($size,1024));
		return @round($size/pow(1024,$i),2).' '.$unit[$i];
	}

	public static function win2utf($in)
	{
		return iconv('windows-1251', 'utf-8', $in);
	}

	public static function utf2win($in)
	{
		return iconv('utf-8', 'windows-1251', $in);
	}

	public static function checkEmail($email)
	{
		$pattern = "/^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/";

		if (preg_match($pattern , strtolower($email)))
			return true;

		return false;
	}

	public static function genPassword($len = 5)
	{
		// make password
		$str = crypt(rand());
		$str = preg_replace("/\W/", "", $str);
		$str = strtolower($str);
		$str = substr($str, 0, $len);

		return $str;
	}

	public static function transliterate($string)
	{
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'y',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'Y',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
		);
		return strtr($string, $converter);
	}

	public static function php_syntax_error($code)
	{
		$dirCache = CoreLib\Config::getConfig('DIR_CACHE');

		$code .= "\n echo 'zzz';";
		$code  = '<?' . $code . '?>';

		//echo DOC_ROOT;exit;

		$fileName = md5(time() . rand(0, 10000)) . '.php';
		$filePath = $dirCache . $fileName;

		Files::saveFile($filePath, $code);

		$cmd = 'php -l ' . $filePath;

		exec($cmd, $out);
		unlink($filePath);

		if (preg_match('/no syntax errors detected/is', $out[0]))
		{
			return false;
		}
		elseif (!trim(implode("\n", $out)))
		{
			return false;
		}
		else
		{
			$res = implode("\n", $out);
			$res = preg_replace('/Errors parsing.+/is', '', $res);

			return trim($res) . "\n";
		}
	}
}