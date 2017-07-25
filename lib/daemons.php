<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;

/*
ps -aux - список процессов
*/
class Daemons
{
	/**
	 * Управляет работой демонов, запускает упавшие.
	 */
	public static function checkDaemons ()
	{
		//Демоны, которые должны работать
		$arRes = Tables\DaemonsTable::getList(
			array(
				'select' => array('ID','NAME','RUNNING','RESTART','PID'),
				'filter' => array(
					'RUN' => true
				)
			)
		);
		if ($arRes)
		{
			foreach ($arRes as &$daemon)
			{
				//Если процесс считается запущенным
				if ($daemon['RUNNING'])
				{
					if ($daemon['PID']>0)
					{
						//Если процесс не запущен
						if (!self::isRun($daemon['PID']))
						{
							//Пробуем запустить
							self::running($daemon);
						}
						static::touchLog($daemon['NAME']);
					}
					//Если нет сохраненного значения PID
					else
					{
						//Если процесс не в ожидании перезапуска
						if (!$daemon['RESTART'])
						{
							//Планируем перезапуск
							self::restart($daemon['ID']);
						}
					}
				}
				//Если процесс остановлен
				else
				{
					//Если PID процесса существует
					if ($daemon['PID']>0)
					{
						//Если процесс запущен
						if (self::isRun($daemon['PID']))
						{
							//Обновляем статус процесса
							self::update($daemon['ID'],array('RUNNING'=>true));
						}
						//Если не запущен, запускаем
						else
						{
							self::running($daemon);
						}
					}
					//Если PID процесса отсутствует
					else
					{
						//Запускаем процесс
						self::running($daemon);
					}
				}
			}
		}

		//Демоны, которые должны быть выключены
		$arRes = Tables\DaemonsTable::getList(
			array(
				'select' => array('ID','RUNNING','PID'),
				'filter' => array(
					'RUN' => false
				)
			)
		);
		if ($arRes)
		{
			foreach ($arRes as $daemon)
			{
				if ($daemon['RUNNING'] && $daemon['PID']>0)
				{
					if (!self::isRun($daemon['PID']))
					{
						self::update($daemon['ID'],array('RUNNING'=>false,'PID'=>0));
					}
				}
				elseif ($daemon['RUNNING'])
				{
					self::update($daemon['ID'],array('RUNNING'=>false,'PID'=>0));
				}
			}
		}
	}

	/**
	 * Проверяет демонов, которые должны быть запущены при старте системы
	 */
	public static function checkDaemonsOnStartUp ()
	{
		$arRes = Tables\DaemonsTable::getList(
			array(
				'select' => array('ID','RUN','RUNNING'),
				'filter' => array(
					'RUN_STARTUP' => true
				)
			)
		);
		if ($arRes)
		{
			foreach ($arRes as $daemon)
			{
				if (!$daemon['RUN'] && !$daemon['RUNNING'])
				{
					self::update($daemon,array("RUN"=>true));
				}
			}
		}
	}

	/**
	 * Останавливает всех демонов.
	 * Если при проверке были найдены
	 *
	 * @return bool
	 */
	public static function stopAllDaemons ()
	{
		$arRes = Tables\DaemonsTable::getList(array(
			'select' => array('ID','RUN'),
			'filter' => array(
				'RUNNING' => true
			)
		));
		if ($arRes !== false)
		{
			foreach ($arRes as $daemon)
			{
				if ($daemon['RUN'])
				{
					self::update($daemon,array("RUN"=>false));
				}
			}

			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Проверяет необходимость перезапуска и остановки определенных демонов
	 *
	 * @param string $daemonName Имя демона
	 *
	 * @return bool
	 */
	public static function needBreak ($daemonName)
	{
		$arRes = Tables\DaemonsTable::getList(
			array(
				'select' => array('ID','RUNNING','RESTART','RUN', 'PID'),
				'filter' => array(
					'NAME' => $daemonName
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
			$daemon = $arRes;

			//Если необходимо перезапустить демона
			if ($daemon['RESTART'])
			{
				$res = self::update(
					$daemon['ID'],
					array(
						'RESTART' => false,
						'PID' => 0,
						'RUNNING' => false
					)
				);
				if ($res->getResult())
				{
					self::log($daemonName,'Daemon planned restart');
					return true;
				}
			}
			//Если необходимо остановить демона
			elseif (!$daemon['RUN'])
			{
				$res = self::update(
					$daemon['ID'],
					array(
						'PID' => 0,
						'RUNNING' => false
					)
				);
				if ($res->getResult())
				{
					self::log($daemonName,'Daemon planned stop');
					return true;
				}
			}
		}

		/*
		$documentRoot = CoreLib\Config::getConfig('DOCUMENT_ROOT');
		if (file_exists($documentRoot.'reboot'))
		{
			return true;
		}
		*/

		return false;
	}

	/**
	 * Устанавливает флаг запущенного демона
	 *
	 * @param int $ID ID записи демона
	 *
	 * @return resource
	 */
	public static function run ($ID)
	{
		return self::update($ID,array('RUN'=>true))->getResult();
	}

	/**
	 * Устанавливает флаг перезапуска демона
	 *
	 * @param int $ID ID записи демона
	 *
	 * @return resource
	 */
	public static function restart ($ID)
	{
		return self::update($ID,array('RESTART'=>true))->getResult();
	}

	/**
	 * Устанавливает флаг остановленного демона
	 *
	 * @param int $ID ID записи демона
	 *
	 * @return resource
	 */
	public static function stop ($ID)
	{
		return self::update($ID,array('RUN'=>false))->getResult();
	}

	/**
	 * Отражает в DB остановку демона
	 *
	 * @param string $daemonName Имя демона
	 */
	public static function stopped ($daemonName)
	{
		$arRes = Tables\DaemonsTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array('NAME'=>$daemonName),
				'limit' => 1
			)
		);
		if ($arRes && isset($arRes[0]))
		{
			$arRes = $arRes[0];
		}
		if (isset($arRes['ID']))
		{
			self::update($arRes['ID'],array('RUNNING'=>false));
		}
	}

	/**
	 * Добавляет запись в лог файл указанного демона
	 *
	 * @param string $daemonName Имя демона
	 * @param string $message Сообщение
	 */
	public static function log ($daemonName, $message)
	{
		$filename = Logs::getLogsDir().'daemon_'.$daemonName.'.log';
		$f1 = fopen ($filename, 'a');
		$tmp=explode(' ', microtime());
		fwrite($f1, date("Y-m-d H:i:s ").$tmp[0].' '.$message."\n------------------\n");
		fclose($f1);
		echo $message;
	}

	public static function touchLog ($daemonName)
	{
		$filename = Logs::getLogsDir().'daemon_'.$daemonName.'.log';
		if (file_exists($filename))
		{
			@touch($filename);
		}
	}

	/**
	 * Функция возвращает путь к папке демонов. Если папка не существует - создает ее
	 *
	 * @return string
	 */
	public static function getDaemonsPath ()
	{
		$daemonsPath = CoreLib\Config::getConfig('DOCUMENT_ROOT').'daemons';
		if (!file_exists($daemonsPath))
		{
			Files::createDir($daemonsPath);
			Files::saveFile($daemonsPath.'/.htaccess','Deny From All');
		}

		return $daemonsPath.'/';
	}

	public static function addNewDaemon (array $arParams)
	{
		if (empty($arParams) || !isset($arParams['NAME']))
		{
			return false;
		}

		$arAdd = array(
			'NAME' => $arParams['NAME']
		);
		if (isset($arParams['DESCRIPTION']))
		{
			if(strlen($arParams['DESCRIPTION'])>255)
			{
				$arParams['DESCRIPTION'] = mb_substr($arParams['DESCRIPTION'],0,255);
			}

			$arAdd['DESCRIPTION'] = $arParams['DESCRIPTION'];
		}
		if (isset($arParams['RUN']))
		{
			$arAdd['RUN'] = $arParams['RUN'];
		}
		if (isset($arParams['RUN_STARTUP']))
		{
			$arAdd['RUN_STARTUP'] = $arParams['RUN_STARTUP'];
		}

		return Tables\DaemonsTable::add(array("VALUES"=>$arAdd))->getInsertId();
	}

	/**
	 * Служебная функция облегающая обновление информации
	 *
	 * @param $primary
	 * @param $arUpdate
	 *
	 * @return CoreLib\DBResult
	 */
	private static function update ($primary, $arUpdate)
	{
		return Tables\DaemonsTable::update($primary,array("VALUES"=>$arUpdate));
	}

	/**
	 * Служебная функция. Стартует указанного демона
	 *
	 * @param int $ID ID записи демона
	 * @param string $name Имя демона
	 *
	 * @return array|bool
	 */
	private static function start ($ID, $name)
	{
		$daemonsPath = self::getDaemonsPath();
		//$outFilename = Files::getLogsDir().'log-daemon_'.$name.'-'.date('Ymd').'.txt';
		$outFilename = '/dev/null';

		$command = 'nohup php -f '.$daemonsPath.'daemon_'.strtolower($name).'.php > '.$outFilename.' 2>&1 & echo $!';
		exec($command ,$op);
		if (intval($op[0])>0)
		{
			$res = self::update(
				intval($ID),
				array(
					"PID" => intval($op[0]),
					"RUNNING" => true
				)
			);
			if (!$res->getResult())
			{
				self::log($name,'Not save in DB daemon PID ['.intval($op[0]).']');
			}

			return array('PID'=>intval($op[0]),'RUNNING'=>true,'RES'=>$res->getResult());
		}
		else
		{
			return false;
		}
	}

	/**
	 * Служебная функция, проверяющая правильность запуска демона.
	 *
	 * @param string $daemon Имя демона
	 */
	private static function running ($daemon)
	{
		$res = self::start($daemon['ID'],$daemon['NAME']);
		if ($res !== false)
		{
			//Процесс запущен
			if (!$res['RES'])
			{
				//Если данные не записались в базу убиваем процесс
				self::kill($res['PID']);
				self::update(
					$daemon['ID'],
					array(
						'PID' => 0,
						'RUNNING' => false
					)
				);
			}
		}
	}

	/**
	 * Служебная функция. Проверяет запущен ли указанный демон
	 *
	 * @param int $PID PID процесса демона
	 *
	 * @return bool
	 */
	private static function isRun ($PID)
	{
		$command = 'ps -p '.$PID;
		exec($command,$op);
		if (!isset($op[1]))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Функция убивает демона shell функцией kill
	 *
	 * @param int $PID PID процесса демона
	 *
	 * @return bool
	 */
	private static function kill ($PID)
	{
		$command = 'kill '.intval($PID);
		exec($command);
		if (self::isRun($PID) === false)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}