<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Entity\Query;
use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;

class Gps
{
	const GPS_LOCATION_RANGE_DEFAULT = 500;
	const EARTH_RADIUS = 6372795;

	public static function parseGpsData ($arData)
	{
		self::log(print_r($arData,true));

		static::parseLocation($arData);
		/*
		if ($arRequest['op'] != '')
		{
			static::processOp($arData);
			return;
		}
		*/


		if (isset($arData['latitude']) && isset($arData['longitude']))
		{
			static::processLocation($arData);
		}

		if (isset($arData['USER']['ID']) && !$arData['LOCATION_FOUND'])
		{
			Objects::setGlobal($arData['USER']['LINKED_OBJECT'].'propSeenAt','в пути');
		}

	}

	public static function log ($strMessage)
	{
		$logsDir = Logs::getLogsDir();
		$today_file = $logsDir . 'log-gps_' . date('Y-m-d') . '.txt';
		$f1 = fopen ($today_file, 'a');
		$tmp=explode(' ', microtime());
		fwrite($f1, date("H:i:s ").$tmp[0].' '.$strMessage."\n------------------\n");
		fclose ($f1);
		@chmod($today_file, Files::getFileChmod());
	}



	private static function parseLocation (&$arData)
	{
		if (isset($arData['location']))
		{
			list($arData['latitude'],$arRequest['longitude']) = explode(',', $arData['location']);
		}
	}

	private static function processLocation (&$arData)
	{
		$device = null;
		if (isset($arData['deviceid']))
		{
			static::processDevice ($arData);
		}

		static::processLog($arData);

		if (isset($arData['DEVICE']['USER_ID']) && intval($arData['DEVICE']['USER_ID'])>0)
		{
			static::processUser ($arData);
		}

		static::checkingLocations ($arData);
	}

	private static function checkingLocations (&$arData)
	{
		// checking locations
		$lat = floatval($arData['latitude']);
		$lon = floatval($arData['longitude']);

		//$locations = SQLSelect("SELECT * FROM gpslocations");
		$arLocations = Tables\GpsLocationsTable::getList(array());
		$total     = count($arLocations);

		$arData['LOCATION_FOUND'] = false;

		foreach ($arLocations as &$location)
		{
			if (!$location['RANGE'])
			{
				$location['RANGE'] = static::GPS_LOCATION_RANGE_DEFAULT;
			}

			$distance = static::calculateTheDistance(
				$lat,
				$lon,
				$location['LAT'],
				$location['LON']
			);

			if ($location['IS_HOME'] && isset($arData['DEVICE']['ID']))
			{
				$arData['DEVICE']['HOME_DISTANCE'] = intval($distance);
				static::updateDevice($arData['DEVICE']);

				if (isset($arData['USER']['ID']))
				{
					$arUserUpdate = array(
						'PROPERTY_HOME_DISTANCE' => $arData['DEVICE']['HOME_DISTANCE'],
						'PROPERTY_HOME_DISTANCE_KM' => round($arData['DEVICE']['HOME_DISTANCE']/1000,1)
					);
					Users::setUserParams($arData['USER']['ID'],$arUserUpdate);
				}

			}

			if ($distance <= $location['RANGE'])
			{
				self::log("Device (" . $arData['DEVICE']['TITLE'] . ") NEAR location " . $location['TITLE']);
				$arData['LOCATION_FOUND'] = true;

				if (isset($arData['USER']['ID']))
				{
					Users::setUserParams($arData['USER']['ID'],array('PROPERTY_SEEN_AT'=>$location['TITLE']));
				}

				$arData['LOG']['LOCATION_ID'] = $location['ID'];
				static::updateLog($arData['LOG']);

				$tmp = Tables\GpsLogTable::getList(
					array(
						'filter' => array(
							'DEVICE_ID' => $arData['DEVICE']['ID'],
							'!ID' => $arData['LOG']['ID']
						),
						'order' => array('ADDED'=>'DESC'),
						'limit' => 1
					)
				);
				if ($tmp && isset($tmp[0]))
				{
					$tmp = $tmp[0];
				}
				if ($tmp['LOCATION_ID'] != $location['ID'])
				{
					static::processEnteredLocation ($arData, $location);
				}
			}
			else
			{
				$tmp = Tables\GpsLogTable::getList(
					array(
						'filter' => array(
							'DEVICE_ID' => $arData['DEVICE']['ID'],
							'!=ID' => $arData['LOG']['ID']
						),
						'order' => array('ADDED'=>'DESC'),
						'limit' => 1
					)
				);
				if ($tmp && isset($tmp[0]))
				{
					$tmp = $tmp[0];
				}
				if ($tmp['LOCATION_ID'] == $location['ID'])
				{
					static::processLeftLocation ($arData, $location);
				}
			}
		}
	}

	private static function processLeftLocation (&$arData, $location)
	{
		Logs::debMes("Device (" . $arData['DEVICE']['TITLE'] . ") LEFT location " . $location['TITLE']);

		$arGpsAction = Tables\GpsActionsTable::getList(
			array(
				'filter' => array(
					'LOCATION_ID' => $location['ID'],
					'ACTION_TYPE' => 0,
					'USER_ID' => $arData['DEVICE']['USER_ID']
				),
				'limit' => 1
			)
		);
		if ($arGpsAction && $arGpsAction[0])
		{
			$arGpsAction = $arGpsAction[0];
		}

		if (isset($arGpsAction['ID']))
		{
			$arGpsAction['EXECUTED'] = date('d.m.Y H:i:s');
			$arGpsAction['LOG']      = $arGpsAction['EXECUTED'] . " Executed\n" . $arGpsAction['LOG'];

			static::updateGpsAction($arGpsAction);

			if (intval($arGpsAction['SCRIPT_ID'])>0)
			{
				Scripts::runScript($arGpsAction['SCRIPT_ID']);
			}
			elseif ($arGpsAction['CODE'])
			{
				try
				{
					$code    = $arGpsAction['CODE'];
					$success = eval($code);

					if ($success === false)
						Logs::debMes("Error in GPS action code: " . $code);
				}
				catch (\Exception $e)
				{
					Logs::debMes('Error: exception ' . get_class($e) . ', ' . $e->getMessage() . '.');
				}
			}
		}
	}

	private static function processEnteredLocation (&$arData, $location)
	{
		Logs::debMes("Device (" . $arData['DEVICE']['TITLE'] . ") ENTERED location " . $location['TITLE']);
		$arGpsAction = Tables\GpsActionsTable::getList(
			array(
				'filter' => array(
					'LOCATION_ID' => $location['ID'],
					'ACTION_TYPE' => 1,
					'USER_ID' => $arData['DEVICE']['USER_ID']
				),
				'limit' => 1
			)
		);
		if ($arGpsAction && isset($arGpsAction[0]))
		{
			$arGpsAction = $arGpsAction[0];
		}

		if (isset($arGpsAction['ID']))
		{
			$arGpsAction['EXECUTED'] = date('d.m.Y H:i:s');
			$arGpsAction['LOG']      = $arGpsAction['EXECUTED'] . " Executed\n" . $arGpsAction['LOG'];

			static::updateGpsAction ($arGpsAction);

			if (intval($arGpsAction['SCRIPT_ID'])>0)
			{
				Scripts::runScript($arGpsAction['SCRIPT_ID']);
			}
			elseif ($arGpsAction['CODE'])
			{
				try
				{
					$code    = $arGpsAction['CODE'];
					$success = eval($code);

					if ($success === false)
					{
						Logs::debMes("Error in GPS action code: " . $code);
						static::registerError('gps_action', "Code execution error: " . $code);
					}
				}
				catch (\Exception $e)
				{
					Logs::debMes('Error: exception ' . get_class($e) . ', ' . $e->getMessage() . '.');
					static::registerError('gps_action', get_class($e) . ', ' . $e->getMessage());
				}
			}
		}
	}

	private static function updateGpsAction ($arGpsAction)
	{
		$actionID = $arGpsAction['ID'];
		unset($arGpsAction['ID']);

		return Tables\GpsActionsTable::update($actionID,array("VALUES"=>$arGpsAction));
	}

	private static function updateLog ($arLog)
	{
		$logID = $arLog['ID'];
		unset($arLog['ID']);
		Tables\GpsLogTable::update($logID,array("VALUES"=>$arLog));

	}

	private static function processUser (&$arData)
	{
		$arUser = Tables\UsersTable::getList(
			array(
				'filter' => array(
					'ID' => $arData['DEVICE']['USER_ID']
				),
				'limit' => 1
			)
		);
		if ($arUser && isset($arUser[0]))
		{
			$arUser = $arUser[0];
		}

		if ($arUser['LINKED_OBJECT'])
		{
			Objects::setGlobal($arUser['LINKED_OBJECT'].'.propCoordinates',$arData['LOG']['LAT'] . ',' . $arData['LOG']['LON']);
			Objects::setGlobal($arUser['LINKED_OBJECT'].'.propCoordinatesUpdated',date("H:i"));
			Objects::setGlobal($arUser['LINKED_OBJECT'].'.propCoordinatesUpdatedTimestamp',time());
			Objects::setGlobal($arUser['LINKED_OBJECT'].'.propBattLevel',$arData['LOG']['BATTLEVEL']);
			Objects::setGlobal($arUser['LINKED_OBJECT'].'.propCharging',$arData['LOG']['CHARGING']);
		}

		$arPrevLog = Tables\GpsLogTable::getList(
			array(
				'filter' => array(
					'!=ID' => $arData['LOG']['ID'],
					'DEVICE_ID' => $arData['DEVICE']['ID']
				),
				'order' => array('ID'=>'DESC'),
				'limit' => 1
			)
		);
		if ($arPrevLog && isset($arPrevLog[0]))
		{
			$arPrevLog = $arPrevLog[0];
		}

		if (isset($arPrevLog['ID']))
		{
			$distance = static::calculateTheDistance(
				$arData['LOG']['LAT'],
				$arData['LOG']['LON'],
				$arPrevLog['LAT'],
				$arPrevLog['LON']
			);

			if ($distance > 100)
			{
				if ($arUser['LINKED_OBJECT'])
				{
					Objects::setGlobal($arUser['LINKED_OBJECT'].'propIsMoving',1);
					Jobs::clearTimeOut('timer_'.$arUser['LINKED_OBJECT'] . '_moving');
					// stopped after 15 minutes of inactivity
					Jobs::setTimeOut('timer_'.$arUser['LINKED_OBJECT'] . '_moving', "MSergeev\\Packages\\Kuzmahome\\Lib\\Objects::setGlobal('" . $arUser['LINKED_OBJECT'].'propIsMoving' . "', 0);", 15 * 60);
				}
			}
		}
		if ($arDistance = static::calculateDistanceToHome($arData['LOG']['LAT'],$arData['LOG']['LON']))
		{
			if ($arUser['LINKED_OBJECT'])
			{
				Objects::setGlobal($arUser['LINKED_OBJECT'].'.propHomeDistance',$arDistance['distance_m']);
				Objects::setGlobal($arUser['LINKED_OBJECT'].'.propHomeDistanceKm',$arDistance['distance_km']);
			}
		}
		$arData['USER'] = $arUser;
	}

	private static function calculateDistanceToHome ($lat,$lon)
	{
		$arReturn = array();
		$arLocation = Tables\GpsLocationsTable::getList(
			array(
				'select' => array('LAT','LON'),
				'filter' => array(
					'IS_HOME' => true
				),
				'limit' => 1
			)
		);
		if ($arLocation && isset($arLocation[0]))
		{
			$arLocation = $arLocation[0];
		}
		if (isset($arLocation['LAT']) && isset($arLocation['LON']))
		{
			$distance = static::calculateTheDistance($lat,$lon,$arLocation['LAT'],$arLocation['LON']);
			$arReturn['distance_m'] = $distance;
			$arReturn['distance_km'] = round(($distance/1000),1);
		}

		if (!empty($arReturn))
		{
			return $arReturn;
		}
		else
		{
			return false;
		}
	}

	private static function processLog (&$arData)
	{
		$arLog = array();

		//$rec['ADDED']     = ($time) ? $time : date('Y-m-d H:i:s');
		$arLog['ADDED']     = date('d.m.Y H:i:s');
		$arLog['LAT']       = $arData['latitude'];
		$arLog['LON']       = $arData['longitude'];
		$arLog['ALT']       = round($arData['altitude'], 2);
		$arLog['PROVIDER']  = $arData['provider'];
		$arLog['SPEED']     = round($arData['speed'], 2);
		$arLog['BATTLEVEL'] = $arData['battlevel'];
		$arLog['CHARGING']  = intval($arData['charging']);
		$arLog['DEVICEID']  = $arData['deviceid'];
		$arLog['ACCURACY']  = isset($arData['accuracy']) ? $arData['accuracy'] : 0;

		if ($arData['DEVICE']['ID'])
		{
			$arLog['DEVICE_ID'] = $arData['DEVICE']['ID'];
		}

		$arLog['ID'] = Tables\GpsLogTable::add(array("VALUES"=>$arLog))->getInsertId();
		$arData['LOG'] = $arLog;
	}

	private static function processDevice (&$arData)
	{
		$arDevice = Tables\GpsDevicesTable::getList(
			array(
				'filter' => array(
					'DEVICEID' => $arData['deviceid']
				),
				'limit' => 1
			)
		);
		if ($arDevice && isset($arDevice[0]))
		{
			$arDevice = $arDevice[0];
		}
		if (!isset($arDevice['ID']))
		{
			$arDevice = array(
				'DEVICEID' => $arData['deviceid'],
				'TITLE' => 'New GPS Device'
			);
			if (isset($arData['device_name']))
			{
				$arDevice['TITLE'] = $arData['device_name'];
			}
			if (isset($arData['token']))
			{
				$arDevice['TOKEN'] = $arData['token'];
			}
			$arDevice['ID'] = Tables\GpsDevicesTable::add(array("VALUES"=>$arDevice))->getInsertId();
			self::log('Add "'.$arDevice['TITLE'].'", DEVICEID='.$arDevice['DEVICEID'].' (ID='.$arDevice['ID'].') ');

			$query = new Query('update');
			$sqlHelper = new CoreLib\SqlHelper(Tables\GpsLogTable::getTableName());
			$sql = "UPDATE\n\t"
				.$sqlHelper->wrapTableQuotes()."\nSET\n\t"
				.$sqlHelper->wrapFieldQuotes('DEVICE_ID')." = '" . $arDevice['ID'] . "'\nWHERE\n\t"
				.$sqlHelper->wrapFieldQuotes('DEVICEID')." = '" . $arDevice['DEVICEID'] . "'";
			$query->setQueryBuildParts($sql);
			$query->exec();
		}
		$arDevice['LAT']     = $arData['latitude'];
		$arDevice['LON']     = $arData['longitude'];
		$arDevice['UPDATED'] = date('d.m.Y H:i:s');

		static::updateDevice($arDevice);
		$arData['DEVICE'] = $arDevice;
	}

	private static function updateDevice ($arDevice)
	{
		$deviceID = $arDevice['ID'];
		unset($arDevice['ID']);

		return Tables\GpsDevicesTable::update($deviceID,array("VALUES"=>$arDevice));
	}

	private static function calculateTheDistance($latA, $lonA, $latB, $lonB)
	{
		$lat1  = $latA * M_PI / 180;
		$lat2  = $latB * M_PI / 180;
		$long1 = $lonA * M_PI / 180;
		$long2 = $lonB * M_PI / 180;

		$cl1 = cos($lat1);
		$cl2 = cos($lat2);
		$sl1 = sin($lat1);
		$sl2 = sin($lat2);

		$delta  = $long2 - $long1;
		$cdelta = cos($delta);
		$sdelta = sin($delta);

		$y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
		$x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

		$ad = atan2($y, $x);

		$dist = round($ad * static::EARTH_RADIUS);

		return $dist;
	}

	private static function registerError ($title, $mess)
	{
		static::log('Error '.$title.': '.$mess);
	}



	private static function processOp (&$arRequest)
	{
		$op = $arRequest['op'];
		$ok = 0;

		if ($op == 'zones')
		{
			//$zones = SQLSelect("SELECT * FROM gpslocations");
			$arRes = Tables\GpsLocationsTable::getList(array());
			echo json_encode(array('RESULT' => array('ZONES' => $arRes, 'STATUS' => 'OK')));
			$ok = 1;
		}

		if ($op == 'add_zone' && $arRequest['latitude'] && $arRequest['longitude'] && $arRequest['title'])
		{
			//global $title;
			//global $range;

			//$sqlQuery = "SELECT * FROM gpslocations WHERE TITLE LIKE '" . DBSafe($title) . "'";
			//$old_location = SQLSelect($sqlQuery);
			$old_location = Tables\GpsLocationsTable::getList(
				array(
					'filter' => array(
						'TITLE' => $arRequest['title']
					),
					'limit' => 1
				)
			);
			if ($old_location && isset($old_location[0]))
			{
				$old_location = $old_location[0];
			}
			if ($old_location['ID'])
			{
				$title = $arRequest['title'].' (1)';
			}
			else
			{
				$title = $arRequest['title'];
			}

			if (!isset($arRequest['range']))
			{
				$range = 200;
			}
			else
			{
				$range = $arRequest['range'];
			}

			$rec = array();

			$rec['TITLE'] = $title;
			$rec['LAT']   = $_REQUEST['latitude'];
			$rec['LON']   = $_REQUEST['longitude'];
			$rec['RANGE'] = floatval($range);
			//$rec['ID']    = SQLInsert('gpslocations', $rec);
			$rec['ID'] = Tables\GpsLocationsTable::add(array("VALUES"=>$rec))->getInsertId();

			echo json_encode(array('RESULT' => array('STATUS' => 'OK')));

			$ok = 1;
		}

		if ($op == 'set_token' && $arRequest['token'] && $arRequest['deviceid'])
		{
			//$sqlQuery = "SELECT * FROM gpsdevices WHERE DEVICEID = '" . DBSafe($_REQUEST['deviceid']) . "'";
			//$device = SQLSelectOne($sqlQuery);
			$device = Tables\GpsDevicesTable::getList(
				array(
					'filter' => array(
						'DEVICEID' => $arRequest['deviceid']
					),
					'limit' => 1
				)
			);
			if ($device && isset($device[0]))
			{
				$device = $device[0];
			}

			if (!isset($device['ID']))
			{
				$device = array();

				$device['DEVICEID'] = $arRequest['deviceid'];
				$device['TOKEN'] = $arRequest['token'];
				$device['TITLE']    = 'New GPS Device';
				//$device['ID']       = SQLInsert('gpsdevices', $device);
				$device['ID'] = Tables\GpsDevicesTable::add(array("VALUES"=>$device))->getInsertId();
			}

			$device['TOKEN'] = $arRequest['token'];
			//SQLUpdate('gpsdevices', $device);
			Tables\GpsDevicesTable::update($device['ID'],array('VALUES'=>array("TOKEN"=>$arRequest['token'])));
			$ok = 1;
		}

		if (!$ok)
			echo json_encode(array('RESULT' => array('STATUS' => 'FAIL')));
	}


}