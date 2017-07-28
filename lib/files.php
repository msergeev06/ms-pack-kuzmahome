<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;

class Files
{
	public static $dirs;
	public static $current_dir;
	public static $current_dest;
	public static $acc;
	public static $ignores;
	public static $files_copied;

	public static function getTreeView ()
	{
		$html = '';
		$dirTextFiles = CoreLib\Config::getConfig('DIR_TEXT_FILES');
		$arFiles = array();
		if (is_dir($dirTextFiles))
		{
			if ($dh = opendir($dirTextFiles))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (!is_dir($dirTextFiles.$file) && $file != "." && $file != ".." && $file != ".htaccess")
					{
						$arInfo = pathinfo($dirTextFiles.$file);
						if ($arInfo['extension']=='txt')
						{
							$arFiles[] = $arInfo;
						}
					}
				}
				closedir($dh);
			}
		}
		//msDebug($arFiles);
		if (!empty($arFiles))
		{
			$html.='<table class="table table-striped" width="100%" border="0" cellpadding="5"><tbody>';

			foreach ($arFiles as $arFile)
			{
				$html.='<tr><td valign="top"><big><a href="add_edit.php?file='.$arFile['filename'].'">test</a></big></td></tr>';
			}

			$html.='</tbody></table>';
		}

		return $html;
	}

	public static function getDirChmod ()
	{
		$dirChmod = CoreLib\Config::getConfig('DIR_CHMOD');
		if (!$dirChmod)
		{
			$dirChmod = 0777;
		}

		return $dirChmod;
	}

	public static function getFileChmod ()
	{
		$fileChmod = CoreLib\Config::getConfig('FILE_CHMOD');
		if (!$fileChmod)
		{
			$fileChmod = 0666;
		}

		return $fileChmod;
	}

	public static function createDir ($path)
	{
		$arPath = explode ('/',$path);
		for ($i=1; $i<count($arPath); $i++)
		{
			$tmpPath = '';
			for ($j=1; $j<=$i; $j++) {
				$tmpPath .= '/'.$arPath[$j];
			}
			if (!file_exists($tmpPath))
			{
				try
				{
					mkdir($tmpPath, self::getDirChmod());
				}
				catch (\Exception $e)
				{
					Logs::debMes('Error not create dirs: '.$tmpPath);
				}
			}
		}
	}

	public static function saveFile ($filename, $data)
	{
		$res = file_put_contents($filename, $data);
		@chmod($filename, self::getFileChmod());

		return $res;

	}

	public static function loadFile ($filename)
	{
		return file_get_contents($filename);
	}

	public static function getFilesTree($destination, $sort = 'name')
	{
		if (substr($destination, -1) == '/' || substr($destination, -1) == '\\')
		{
			$destination = substr($destination, 0, strlen($destination) - 1);
		}

		$res = array();

		if (!is_dir($destination))
			return $res;

		if ($dir = @opendir($destination))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (is_dir($destination . "/" . $file) && ($file != '.') && ($file != '..'))
				{
					$tmp = self::getFilesTree($destination . "/" . $file);
					if (is_array($tmp))
					{
						foreach ($tmp as $elem)
						{
							$res[] = $elem;
						}
					}
				}
				elseif (is_file($destination . "/" . $file))
				{
					$res[] = ($destination . "/" . $file);
				}
			}
			closedir($dir);
		}

		if ($sort == 'name')
		{
			sort($res, SORT_STRING);
		}

		return $res;
	}

	/**
	 * getLogsDir
	 *
	 * @deprecated
	 * @see Logs::getLogsDir
	 * @return string
	 */
	public static function getLogsDir ()
	{
		return Logs::getLogsDir();
	}

	public static function getCachedDir ()
	{
		$cachedDir = CoreLib\Config::getConfig('DIR_CACHE');
		self::createDir($cachedDir);

		return $cachedDir;
	}

	/**
	 * @deprecated
	 * @see Daemons::getDaemonsPath()
	 * @return string
	 */
	public static function getDaemonsPath ()
	{
		return Daemons::getDaemonsPath();
	}

	public static function getRandomLine($filename)
	{
		$dirTextFiles = CoreLib\Config::getConfig('DIR_TEXT_FILES');

		if (file_exists($dirTextFiles . $filename . '.txt'))
		{
			$filename = $dirTextFiles . $filename . '.txt';

			$arFile = array();
			$f1 = fopen($filename, 'r');
			while (!feof($f1))
			{
				$arFile[] = fgets($f1);
			}
			fclose($f1);

			$total = count($arFile);
			$line  = $arFile[rand(0, ($total - 1))];

			if ($line != '')
			{
				return $line;
			}
		}

		return false;
	}






	public static function preparePathTime($s, $mtime)
	{
		// %d #d &d $d
		$symbs = array('a', 'A', 'B', 'd', 'D', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'l', 'L',
			'm', 'M', 'n', 'O', 'r', 's', 'S', 't', 'T', 'U', 'w', 'W', 'Y', 'y', 'z', 'Z');

		foreach ($symbs as $v)
		{
			$s = str_replace('$' . $v, date($v), $s);
			$s = str_replace('%' . $v, date($v, $mtime), $s);
		}

		return $s;
	}

	public static function is_dir2 ($d)
	{
		$d = str_replace('NET:', '//', $d);

		if (is_dir($d))
			return 1;

		if ($node = @opendir($d))
		{
			closedir($node);
			return 1;
		}

		return 0;
	}

	public static function remove_old_files ($path, $days)
	{
		$mtime = filemtime($path);
		$diff  = round((time() - $mtime) / 60 / 60 / 24, 2);

		if ($diff > $days)
		{
			echo 'Removing ' . $path . ' (' . $diff . " days old)\n";
			unlink($path);
		}
	}

	public static function copyNewFile($path, $days)
	{
		$dirs = &static::$dirs;
		$current_dir = &static::$current_dir;
		$current_dest = &static::$current_dest;
		$acc = &static::$acc;
		$ignores = &static::$ignores;

		$mtime = filemtime($path);
		$diff  = round((time() - $mtime) / 60 / 60 / 24, 2);

		if ($diff > $days)
		{
			return 0;
		}

		foreach ($ignores as $ptn)
		{
			if (preg_match("/" . $ptn . "/is", $path))
				return 0;
		}

		$tmdiff = 0;

		$dest = (!$current_dest) ? $dirs[$current_dir] : $current_dest;
		$dest = str_replace($current_dir, $dest, $path);

		$dest_path     = str_replace(basename($dest), '', $dest);
		$new_dest_path = static::preparePathTime($dest_path, $mtime);

		$dest      = str_replace($dest_path, $new_dest_path, $dest);
		$dest_path = $new_dest_path;

		if (!static::is_dir2($dest_path))
		{
			if (!static::makedir($dest_path))
				return 0;
		}

		if (!file_exists($dest))
		{
			echo $path . " -> " . $dest . " (new)\n";
			static::copyFile($path, $dest);
		}
	}

	public static function checkfile($path, $move)
	{
		$dirs = &static::$dirs;
		$current_dir = &static::$current_dir;
		$current_dest = &static::$current_dest;
		$acc = &static::$acc;
		$ignores = &static::$ignores;
		$files_copied = &static::$files_copied;

		foreach ($ignores as $ptn)
		{
			if (preg_match("/" . $ptn . "/is", $path))
				return 0;
		}

		$tmdiff = 0;
		$dest   = (!$current_dest) ? $dirs[$current_dir] : $current_dest;
		$path   = str_replace('NET:', '//', $path);

		$current_dir = str_replace('NET:', '//', $current_dir);

		$mtime = filemtime($path);

		$dest      = str_replace('NET:', '//', $dest);
		$dest      = str_replace($current_dir, $dest, $path);
		$dest_path = str_replace(basename($dest), '', $dest);

		$new_dest_path = static::preparePathTime($dest_path, $mtime);
		$dest          = str_replace($dest_path, $new_dest_path, $dest);
		$dest_path     = $new_dest_path;

		if (!static::is_dir2($dest_path))
		{
			if (!static::makedir($dest_path))
				return 0;
		}

		if (!file_exists($dest))
		{
			echo $path . " -> " . $dest . " (new)\n";
			static::copyFile($path, $dest);
		}
		else
		{
			$dest_size = filesize($dest);
			$src_size  = filesize($path);
			$tmdiff    = filemtime($path) - filemtime($dest);

			if ($tmdiff > $acc || ($dest_size == 0 && $src_size != 0))
			{
				$status = "updated $tmdiff";
				echo $path . " -> " . $dest . " (updated " . round($tmdiff / 60 / 60, 1) . " h)\n";
				static::copyFile($path, $dest);
			}
			else
			{
				//echo $path." -> ".$dest." (OK ".round($tmdiff/60/60, 1)." h)\n";
				$fs = filesize($path);
				//if ($fs>(2*1024*1024)) {
				$k = basename($path) . '_' . $fs;
				//$files_copied[$k]=$dest;
				//}
			}
		}

		if ($move)
			unlink($path);
	}

	public static function copyFile($src, $dst)
	{
		$files_copied = &static::$files_copied;

		$size_limit = 2000 * 1024 * 1024;

		$fs = filesize($src);

		if ($fs == 0)
			return;

		$fs_mb = round($fs / 1024 / 1024, 2);

		if ($fs > $size_limit)
		{
			$k = basename($src) . '_' . $fs;

			if ($files_copied[$k] == '')
			{
				echo "Size: " . $fs_mb . "Mb\n";

				$src = str_replace('/', '\\', $src);
				$dst = str_replace('/', '\\', $dst);

				system('copy "' . $src . '" "' . $dst . '"'); // long copy
				$files_copied[$k] = $dst;
			}
			else
			{
				echo " already copied to (" . $files_copied[$k] . ")\n";
			}
		}
		else
		{
			copy($src, $dst);
		}

		touch($dst, filemtime($src));
	}

	public static function walk_dir($dir, $callback, $move = 0)
	{
		$ignores = &static::$ignores;

		$dir  = str_replace('NET:', '//', $dir);
		$dir .= '/';

		foreach ($ignores as $ptn)
		{
			if (preg_match("/" . $ptn . "/is", $dir))
				return;
		}

		//if (!preg_match('/mail.ru Blogs/is', $dir)) {
		// return;
		//}
		echo "processing $dir\n";

		if (!static::is_dir2($dir))
			return;

		$handle = opendir($dir);

		while (false !== $thing = readdir($handle))
		{
			if ($thing == '.' || $thing == '..' ) continue;

			$thing = $dir . $thing;

			if (static::is_dir2($thing))
				static::walk_dir($thing, $callback , $move);
			elseif (is_file($thing))
				call_user_func($callback, $thing , $move);
		}

		closedir($handle);
	}

	function walk_dir2($dir, $callback, $move = 0)
	{
		$ignores = &static::$ignores;
		$dirs = &static::$dirs;
		$acc = &static::$acc;
		$current_dir = &static::$current_dir;
		$current_dest = &static::$current_dest;

		$dir  = str_replace('NET:', '//', $dir);
		$dir .= '/';

		foreach ($ignores as $ptn)
		{
			if (preg_match("/" . $ptn . "/is", $dir))
				return;
		}

		$tmpdir = $current_dir;
		$tmpdir = str_replace('NET:', '//', $tmpdir);
		$dest   = $dirs[$dir];
		$dest   = str_replace($tmpdir, $current_dest, $dir);

		// ADDING NEW/UPDATED FILES
		$processed = array();

		//$dir=str_replace('/', '\\', $dir);
		if (!static::is_dir2($dir))
		{
			echo "Dir not found: $dir\n";
			return;
		}

		$handle = opendir($dir);

		while (false !== $thing = readdir($handle))
		{
			if ($thing == '.' || $thing == '..')
				continue;

			$processed[$thing] = 1;

			$thing = $dir . $thing;

			if (static::is_dir2($thing))
				static::walk_dir2($thing, $callback , $move);
			elseif (is_file($thing))
				call_user_func($callback, $thing , $move);
		}

		closedir($handle);

		// print_r($processed);

		// REMOVING FILES
		$handle = opendir($dest);

		while (false !== $thing = readdir($handle))
		{
			if ($thing == '.' || $thing == '..' )
				continue;

			if (!$processed[$thing])
			{
				if (is_file($dest . $thing))
				{
					echo "Removing file: " . $dest . $thing . " \n";
					unlink($dest . $thing);
				}
				elseif (static::is_dir2($dest . $thing))
				{
					echo "Removing dir: " . $dest . $thing . " \n";
					static::removeTree($dest . $thing);
				}
			}
		}

		closedir($handle);
		// exit;
	}

	public static function makeDir($dir, $sep = '/')
	{
		$tmp    = explode($sep, $dir);
		$tmpCnt = count($tmp);
		$cr     = "";

		$bReturn = true;

		for ($i = 0; $i < $tmpCnt; $i++)
		{
			$cr .= $tmp[$i] . "$sep";

			if (!static::is_dir2($cr))
			{
				echo "Making folder [$cr]\n";
				$b = mkdir($cr);
				if (!$b) $bReturn = false;
			}
		}

		return $bReturn;
	}

	public static function removeTree($destination)
	{
		$res = 1;

		if (!static::is_dir2($destination))
			return 0; // cannot create destination path

		if ($dir = @opendir($destination))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (static::is_dir2($destination . "/" . $file) && ($file != '.') && ($file != '..'))
				{
					$res = static::removeTree($destination . "/" . $file);
				}
				elseif (is_file($destination . "/" . $file))
				{
					$res = unlink($destination . "/" . $file);
				}
			}
			closedir($dir);

			$res = rmdir($destination);
		}

		return $res;
	}

	function processLines($data)
	{
		$ignores = &static::$ignores;

		$hash  = array();
		$data  = str_replace("\r", '', $data);
		$lines = explode("\n", $data);
		$total = count($lines);

		for ($i = 0; $i < $total; $i++)
			static::processLine($lines[$i], $hash);

		return $total;
	}

	public static function processLine($line, $hash = '')
	{
		$current_dest = &static::$current_dest;
		$current_dir = &static::$current_dir;
		$ignores = &static::$ignores;

		if (!is_array($ignores))
			$ignores = array();

		if (!is_array($hash))
			$hash = array();

		$line = trim($line);

		foreach ($hash as $k => $v)
			$line = str_replace($k, $v, $line);

		echo $line . "\n";

		if (preg_match('/^\/\//', $line))
		{
			return;
		}
		elseif (preg_match('/^IGNORE (.+?)$/i', $line, $matches))
		{
			$ignores[] = trim($matches[1]);
		}
		elseif (preg_match('/^SET (.+?)=(.+?)$/i', $line, $matches))
		{
			$key        = trim($matches[1]);
			$value      = trim($matches[2]);
			$hash[$key] = $value;
		}
		elseif (preg_match('/^CLEAR (.+?) (\d+) DAYS OLD$/is', $line, $matches))
		{
			$from = trim($matches[1]);

			$current_dir = $from;

			$days = (int)($matches[2]);

			if ($days > 0)
				static::walk_dir($from, "remove_old_files", $days);
		}
		elseif (preg_match('/^(.+?)\+>(.+?) (\d+) DAYS OLD$/is', $line, $matches))
		{
			$from = trim($matches[1]);
			$to   = trim($matches[2]);

			$current_dir  = $from;
			$current_dest = $to;

			$days = (int)($matches[3]);

			static::walk_dir($from, "copyNewFile", $days);
		}
		elseif (preg_match('/^(.+?)<\+(.+?) (\d+) DAYS OLD$/is', $line, $matches))
		{
			$to   = trim($matches[1]);
			$from = trim($matches[2]);

			$current_dir  = $from;
			$current_dest = $to;

			/*
			  if (!is_dir2($to) && !@mkdir($to)) {
			  echo "\n Cannot make destination dir ($to)\n";
			  return;
			  }
			   */

			$days = (int)($matches[3]);

			static::walk_dir($from, "copyNewFile", $days);
		}
		elseif (preg_match('/^(.+?)=>(.+?)$/is', $line, $matches))
		{
			$from = trim($matches[1]);
			$to   = trim($matches[2]);

			$current_dir  = $from;
			$current_dest = $to;

			/*
			  if (!is_dir2($to) && !@mkdir($to)) {
			  echo "\n Cannot make destination dir ($to)\n";
			  return;
			  }
			   */
			//echo "walking $from\n";

			static::walk_dir($from, "checkfile");
		}
		elseif (preg_match('/^(.+?)<=(.+?)$/is', $line, $matches))
		{
			$from = trim($matches[2]);
			$to   = trim($matches[1]);

			$current_dir  = $from;
			$current_dest = $to;

			/*
			  if (!is_dir2($to) && !@mkdir($to)) {
			  return;
			  }
			   */

			static::walk_dir($from, "checkfile");
		}
		elseif (preg_match('/^(.+?)\!>(.+?)$/is', $line, $matches))
		{
			$from = trim($matches[1]);
			$to   = trim($matches[2]);

			$current_dir  = $from;
			$current_dest = $to;

			/*
			  if (!is_dir2($to) && !@mkdir($to)) {
			  return;
			  }
			   */

			static::walk_dir2($from, "checkfile");
		}
		elseif (preg_match('/^(.+?)<\!(.+?)$/is', $line, $matches))
		{
			$from = trim($matches[2]);
			$to   = trim($matches[1]);

			$current_dir  = $from;
			$current_dest = $to;

			/*
			  if (!is_dir2($to) && !@mkdir($to)) {
			  return;
			  }
			   */

			static::walk_dir2($from, "checkfile");
		}
		elseif (preg_match('/^(.+?)->(.+?)$/is', $line, $matches))
		{
			$from = trim($matches[1]);
			$to   = trim($matches[2]);

			$current_dir  = $from;
			$current_dest = $to;

			/*
			  if (!is_dir2($to) && !@mkdir($to)) {
			  return;
			  }
			   */

			static::walk_dir($from, "checkfile", 1);
		}
		elseif (preg_match('/^(.+?)<-(.+?)$/is', $line, $matches))
		{
			$from = trim($matches[2]);
			$to   = trim($matches[1]);

			$current_dir  = $from;
			$current_dest = $to;

			/*
			  if (!is_dir2($to) && !@mkdir($to)) {
			  return;
			  }
			   */

			static::walk_dir($from, "checkfile", 1);
		}
		// echo $line."\n";
	}

	public static function UTF_Encode($str, $type)
	{
		static $conv = '';

		if (!is_array($conv))
		{
			$conv = array();

			for ($x = 128; $x <= 143; $x++)
			{
				$conv['utf'][] = chr(209) . chr($x);
				$conv['win'][] = chr($x + 112);
			}

			for ($x = 144; $x <= 191; $x++)
			{
				$conv['utf'][] = chr(208) . chr($x);
				$conv['win'][] = chr($x + 48);
			}

			$conv['utf'][] = chr(208) . chr(129);
			$conv['win'][] = chr(168);
			$conv['utf'][] = chr(209) . chr(145);
			$conv['win'][] = chr(184);
		}

		if ( $type == 'w')
			return str_replace($conv['utf'], $conv['win'], $str);
		elseif ($type == 'u')
			return str_replace($conv['win'], $conv['utf'], $str);
		else
			return $str;
	}

	public static function copyTree($source, $destination, $over = 0)
	{
		$res = 1;

		if (!is_dir($source))
		{
			return 0; // incorrect source path
		}

		if (!is_dir($destination))
		{
			if (!mkdir($destination, 0777))
			{
				return 0; // cannot create destination path
			}
		}

		if ($dir = @opendir($source))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (is_dir($source . "/" . $file) && ($file != '.') && ($file != '..'))
				{
					$res = static::copyTree($source . "/" . $file, $destination . "/" . $file, $over);
				}
				elseif (is_file($source . "/" . $file) && (!file_exists($destination . "/" . $file) || $over))
				{
					$res = copy($source . "/" . $file, $destination . "/" . $file);
				}
			}
			closedir($dir);
		}

		return $res;
	}
}