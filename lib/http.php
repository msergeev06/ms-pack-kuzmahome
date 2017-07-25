<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Core\Entity\Query;
use MSergeev\Packages\Kuzmahome\Tables;

class Http
{
	public static function checkAutorize ()
	{
		//TODO: Добавить возможность авторизовываться различным пользователям под различными паролями

		$homeNetwork = CoreLib\Config::getConfig('HOME_NETWORK');
		if ($homeNetwork && $homeNetwork != '' && !isset($argv[0])
			&& (!(preg_match('/\/gps\.php/is', $_SERVER['REQUEST_URI'])
					|| preg_match('/\/trackme\.php/is', $_SERVER['REQUEST_URI'])
					|| preg_match('/\/btraced\.php/is', $_SERVER['REQUEST_URI']))
				|| $_REQUEST['op'] != '')
			&& !preg_match('/\/rss\.php/is', $_SERVER['REQUEST_URI'])
			&& 1)
		{
			$p = preg_quote($homeNetwork);
			$p = str_replace('\*', '\d+?', $p);
			$p = str_replace(',', ' ', $p);
			$p = str_replace('  ', ' ', $p);
			$p = str_replace(' ', '|', $p);

			$remoteAddr = getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : $_SERVER["REMOTE_ADDR"];

			if (!preg_match('/' . $p . '/is', $remoteAddr) && $remoteAddr != '127.0.0.1')
			{
				if (!isset($_SERVER['PHP_AUTH_USER']))
				{
					header("WWW-Authenticate: Basic realm=\"KuzmaHome\"");
					header("HTTP/1.0 401 Unauthorized");
					echo "Authorization required\n";
					exit;
				}
				else
				{
					//if ($_SERVER['PHP_AUTH_USER'] != 'msergeev' || $_SERVER['PHP_AUTH_PW'] != 'hKjpTg3VCg')
					if (!CoreLib\Users::logIn($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'],true))
					{
						// header("Location:$PHP_SELF\n\n");
						header("WWW-Authenticate: Basic realm=\"KuzmaHome\"");
						header("HTTP/1.0 401 Unauthorized");
						echo "Authorization required\n";
						exit;
					}
				}
			}
		}
	}

	public static function getURL($url, $cache = 0, $username = '', $password = '')
	{
		$cacheDir = CoreLib\Config::getConfig('DIR_CACHE');
		$cache_file = $cacheDir.'urls/' . preg_replace('/\W/is', '_', str_replace('http://', '', $url)) . '.html';
		$result = false;

		if (!$cache || !is_file($cache_file) || (time() - filemtime($cache_file)) > $cache)
		{
			try
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:32.0) Gecko/20100101 Firefox/32.0');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);     // bad style, I know...
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt($ch, CURLOPT_TIMEOUT, 15);

				if ($username != '' || $password != '')
				{
					curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
					curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
				}

				$tempFileName = $cacheDir . 'cookie.txt';
				curl_setopt($ch, CURLOPT_COOKIEJAR, $tempFileName);
				curl_setopt($ch, CURLOPT_COOKIEFILE, $tempFileName);

				$result = curl_exec($ch);
			}
			catch (\Exception $e)
			{
				Logs::debMes('geturl error: ' . $url . ' ' . get_class($e) . ', ' . $e->getMessage());
			}

			if ($cache > 0)
			{
				Files::createDir($cacheDir . 'urls');
				if ($result !== false)
				{
					Files::saveFile($cache_file, $result);
				}
			}

		}
		else
		{
			$result = Files::loadFile($cache_file);
		}

		return $result;
	}

	public static function isOnline($host)
	{
		$query = new Query('select');
		$sqlHelper = new CoreLib\SqlHelper(Tables\PingHostsTable::getTableName());
		$sql = "SELECT *\n"
			."FROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\n"
			."WHERE\n\t"
			.$sqlHelper->wrapFieldQuotes('HOSTNAME')." LIKE '" . $host . "' OR\n\t"
			.$sqlHelper->wrapFieldQuotes('TITLE')." LIKE '" . $host . "'";
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		if ($ar_res = $res->fetch())
		{
			if ($ar_res['ONLINE']===true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		return NULL;
	}

	public static function ping($host)
	{
		exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);

		return $rval === 0 && preg_match('/ttl/is', join('', $res));
	}

	public static function checkAllHosts ($limit=1000)
	{
		$query = new Query('select');
		$sqlHelper = new CoreLib\SqlHelper(Tables\PingHostsTable::getTableName());
		$sql = "SELECT *\nFROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\n"
			."WHERE\n\t"
			.$sqlHelper->wrapFieldQuotes('CHECK_NEXT')."<=NOW()\n"
			."ORDER BY\n\t"
			.$sqlHelper->wrapFieldQuotes('CHECK_NEXT')." ASC\n"
			."LIMIT ".$limit;
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		if ($res->getResult())
		{
			$arHosts = array();
			while ($ar_res = $res->fetch())
			{
				$arHosts[] = $ar_res;
			}
			if (!empty($arHosts))
			{
				foreach ($arHosts as $host)
				{
					$online_interval=$host['ONLINE_INTERVAL'];
					if (!$online_interval) {
						$online_interval=60;
					}
					$offline_interval=$host['OFFLINE_INTERVAL'];
					if (!$offline_interval) {
						$offline_interval=$online_interval;
					}
					Tables\PingHostsTable::update($host['ID'],array('VALUES'=>array('CHECK_NEXT'=>$host['CHECK_NEXT'])));

					$online=self::ping($host['HOSTNAME']);
					if ($online)
					{
						$new_status = true;
					}
					else
					{
						$new_status = false;
					}
					$old_status = $host['ONLINE'];

					if ($host['COUNTER_REQUIRED'])
					{
						if ($old_status != $new_status)
						{
							if ($host['COUNTER_REQUIRED'] > $host['COUNTER_CURRENT'])
							{
								$host['COUNTER_CURRENT']++;
								$host['LOG']=date('d.m.Y H:i:s').' tries counter increased to '.$host['COUNTER_CURRENT'].' (status: '.(($new_status)?'online':'offline').')'."\n".$host['LOG'];
							}
							else
							{
								$host['ONLINE'] = $new_status;
								$host['COUNTER_CURRENT'] = 0;
								$host['LOG']=date('d.m.Y H:i:s').' tries counter reset (status: '.(($new_status)?'online':'offline').')'."\n".$host['LOG'];
								if ($host['ONLINE']) {
									$host['LOG']=date('d.m.Y H:i:s').' Host is online'."\n".$host['LOG'];
								}
								else
								{
									$host['LOG']=date('d.m.Y H:i:s').' Host is offline'."\n".$host['LOG'];
								}
							}
						}
					}
					else
					{
						$host['ONLINE'] = $new_status;
						$host['COUNTER_CURRENT'] = 0;
						$host['LOG']=date('d.m.Y H:i:s').' tries counter reset (status: '.(($new_status)?'online':'offline').')'."\n".$host['LOG'];
						//msEchoVar($host['LOG']);
						if ($host['ONLINE']) {
							$host['LOG']=date('d.m.Y H:i:s').' Host is online'."\n".$host['LOG'];
						}
						else
						{
							$host['LOG']=date('d.m.Y H:i:s').' Host is offline'."\n".$host['LOG'];
						}
						//msEchoVar($host['LOG']);
					}

					$host['CHECK_LATEST']=date('d.m.Y H:i:s');

					if ($host['ONLINE'])
					{
						$host['CHECK_NEXT']=date('d.m.Y H:i:s', time()+$online_interval);
					}
					else
					{
						$host['CHECK_NEXT']=date('d.m.Y H:i:s', time()+$offline_interval);
					}

					$tmp=explode("\n", $host['LOG']);
					$total=count($tmp);
					if ($total > 30) {
						$tmp=array_slice($tmp, 0, 30);
						$host['LOG']=implode("\n", $tmp);
					}
					$hostID = $host['ID'];
					unset($host['ID']);
					Tables\PingHostsTable::update($hostID,array("VALUES"=>$host));

					if ($old_status != $host['ONLINE'])
					{
						$run_script_id=0;
						$run_code='';
						if ($host['ONLINE'])
						{
							if (intval($host['ONLINE_SCRIPT_ID'])>0)
							{
								$run_script_id = $host['ONLINE_SCRIPT_ID'];
							}
							elseif ($host['ONLINE_CODE'])
							{
								$run_code = $host['ONLINE_CODE'];
							}
						}
						else
						{
							if (intval($host['OFFLINE_SCRIPT_ID'])>0)
							{
								$run_script_id = $host['OFFLINE_SCRIPT_ID'];
							}
							elseif ($host['OFFLINE_CODE'])
							{
								$run_code = $host['OFFLINE_CODE'];
							}
						}

						if ($run_script_id)
						{
							Scripts::runScript($run_script_id);
						}
						elseif ($run_code)
						{
							try {
								$code=$run_code;
								$success=eval($code);
								if ($success===false) {
									Logs::debMes("Error in hosts online code: ".$code);
								}
							} catch(\Exception $e){
								Logs::debMes('Error: exception '.get_class($e).', '.$e->getMessage().'.');
							}
						}
					}
				}
			}
		}

	}
}