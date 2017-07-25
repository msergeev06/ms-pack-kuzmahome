<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;

class Bluetooth
{
	private static $bts_cmd = 'sudo hcitool scan | grep ":"';
	private static $skip_counter = 0;
	private static $data = '';
	private static $first_run = 1;
	private static $last_scan = null;
	private static $bt_devices = array();

	/*
	public static function daemon ()
	{
		$skip_counter++;
		if ($skip_counter >= 30)
		{
			$skip_counter = 0;
			$data = '';

			$str=exec($bts_cmd, $bt_scan_arr);

			$lines = array();
			$btScanArrayLength = count($bt_scan_arr);

			for ($i = 0; $i < $btScanArrayLength; $i++)
			{
				if (!$bt_scan_arr[$i]) {
					continue;
				}
				$btstr      = explode("\t", $bt_scan_arr[$i]);
				$btaddr[$i] = $btstr[1];
				$btname[$i] = rtrim($btstr[2]);
				$lines[]    = $i . "\t" . $btname[$i] . "\t" . $btaddr[$i];
			}

			$data = implode("\n",$lines);

			$last_scan = time();

			if ($data)
			{
				$data = str_replace(chr(0), '', $data);
				$data = str_replace("\r", '', $data);
				$lines = explode("\n", $data);
				$total = count($lines);

				for ($i = 0; $i < $total; $i++)
				{
					$fields = explode("\t", $lines[$i]);
					$title  = trim($fields[1]);
					$mac    = trim($fields[2]);
					$user   = array();

					if ($mac != '')
					{
						if (!$bt_devices[$mac])
						{
							// && !$first_run
							//new device found
							echo date('Y/m/d H:i:s') . ' Device found: ' . $mac . '\n';

							$sqlQuery = "SELECT *
                                 FROM btdevices
                                WHERE MAC LIKE '" . $mac . "'";

							$rec = SQLSelectOne($sqlQuery);
							$previous_found = $rec['LAST_FOUND'];
							$rec['LAST_FOUND'] = date('Y/m/d H:i:s');
							$rec['LOG'] = 'Device found ' . date('Y/m/d H:i:s') . "\n" . $rec['LOG'];

							if (!$rec['ID'])
							{
								$rec['FIRST_FOUND'] = $rec['LAST_FOUND'];
								$previous_found = $rec['LAST_FOUND'];
								$rec['MAC'] = strtolower($mac);

								if ($title != '')
									$rec['TITLE'] = 'Устройство: ' . $title;
								else
									$rec['TITLE'] = 'Новое устройство';

								$new = 1;

								SQLInsert('btdevices', $rec);
							}
							else
							{
								$new = 0;

								if ($rec['USER_ID'])
								{
									$sqlQuery = "SELECT *
                                       FROM users
                                      WHERE ID = '" . $rec['USER_ID'] . "'";

									$user = SQLSelectOne($sqlQuery);
								}

								SQLUpdate('btdevices', $rec);
							}

							$objectArray = array('mac'            => $mac,
							                     'user'           => $user['NAME'],
							                     'new'            => $new,
							                     'previous_found' => $previous_found,
							                     'last_found'     => $rec['FIRST_FOUND']);

							$obj=getObject('BlueDev');
							if (is_object($obj)) {
								$obj->raiseEvent("Found", $objectArray);
							}
						}
						else
						{
							$sqlQuery = "SELECT *
                                 FROM btdevices
                                WHERE MAC = '" . $mac . "'";

							$rec = SQLSelectOne($sqlQuery);
							$rec['LAST_FOUND'] = date('Y/m/d H:i:s');

							if ($title != '')
							{
								$rec['TITLE'] = 'Устройство: ' . $title;
							}

							if ($rec['ID'])
								SQLUpdate('btdevices', $rec);
						}

						$bt_devices[$mac] = $last_scan;
					}
				}

				foreach ($bt_devices as $k => $v)
				{
					if ($v != $last_scan)
					{
						//device removed
						echo date('Y/m/d H:i:s') . ' Device gone: ' . $k . '\n';

						$user = array();
						$sqlQuery = "SELECT *
                              FROM btdevices
                             WHERE MAC = '" . $k . "'";

						$rec  = SQLSelectOne($sqlQuery);

						if ($rec['ID'])
						{
							$rec['LOG'] = 'Device lost ' . date('Y/m/d H:i:s') . '\n' . $rec['LOG'];
							SQLUpdate('btdevices', $rec);

							if ($rec['USER_ID'])
							{
								$sqlQuery = "SELECT *
                                    FROM users
                                   WHERE ID = '" . $rec['USER_ID'] . "'";

								$user = SQLSelectOne($sqlQuery);
							}
						}

						$objectArray = array('mac'  => $k,
						                     'user' => $user['NAME']);


						$obj=getObject('BlueDev');
						if (is_object($obj)) {
							$obj->raiseEvent("Lost", $objectArray);
						}


						unset($bt_devices[$k]);
					}
				}
			}
		}

		$first_run = 0;
	}
	*/
}