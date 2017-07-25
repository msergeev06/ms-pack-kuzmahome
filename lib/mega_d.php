<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;

class MegaD
{
	const DEF_TYPE_OPTIONS = 'automation=Automation|light=Light controller|dimmer=Dimmer controller';

	public static function processRequest ()
	{
		$ip=$_SERVER['REMOTE_ADDR'];

		$arRec = Tables\MegadDevicesTable::getList(
			array(
				'filter' => array('IP'=>$ip),
				'limit' => 1
			)
		);
		if ($arRec && isset($arRec[0]))
		{
			$arRec = $arRec[0];
		}
		if (!$arRec['ID'])
		{
			$arRec = array(
				'IP' => $ip,
				'TITLE' => 'MegaD '.$ip,
				'MDID' => uniqid("megad")
			);
			$arRec['ID'] = Tables\MegadDevicesTable::add(array("VALUES"=>$arRec))->getInsertId();

			static::readConfig($arRec['ID']);
		}
		else
		{
			//processing
			/*
			   global $pt; //port
			   global $at; // internal temperature
			   global $v; // value for ADC
			   global $dir; //direction 1/0
			   global $cnt; //counter
			   global $all;
			   */
			if (isset($_GET['v']))
			{
				$v=$_GET['v'];
			}

			$m=$_GET['m'];
			$pt=$_GET['pt'];
			$at=$_GET['at'];
			$dir=$_GET['dir'];
			$cnt=$_GET['cnt'];

			if (isset($_GET['all']))
			{
				static::readValues($arRec['ID'], $_GET['all']);
			}

			//input data changed
			if (isset($pt))
			{
				//$prop=SQLSelectOne("SELECT * FROM megadproperties WHERE DEVICE_ID=".$arRec['ID']." AND NUM='".DBSafe($pt)."'");
				$prop = Tables\MegadPropertiesTable::getList(
					array(
						'filter' => array(
							'DEVICE_ID' => $arRec['ID'],
							'NUM' => $pt
						),
						'limit' => 1
					)
				);
				if ($prop && isset($prop[0]))
				{
					$prop = $prop[0];
				}
				if ($prop['ID'])
				{
					if ($prop['ECMD'] && !($prop['SKIP_DEFAULT']))
					{
						$ecmd=$prop['ECMD'];
					}

					unset($value2);

					if (isset($v))
					{
						$value=$v;
					}
					else
					{
						if ($m=='1')
						{
							$value=0;
						}
						else
						{
							$value=1;
						}
					}

					if (isset($cnt))
					{
						$prop['COUNTER']=$cnt;
						$value2=$prop['COUNTER'];
					}

					$old_value=$prop['CURRENT_VALUE_STRING'];
					$old_value2=$prop['CURRENT_VALUE_STRING2'];

					$prop['CURRENT_VALUE_STRING']=$value;
					$prop['UPDATED']=date('d.m.Y H:i:s');
					//SQLUpdate('megadproperties', $prop);
					static::updateProperty($prop);

					/*
					if ($prop['LINKED_OBJECT'] && $prop['LINKED_PROPERTY'])
					{
						if ($old_value!=$prop['CURRENT_VALUE_STRING'] || $prop['CURRENT_VALUE_STRING']!=gg($prop['LINKED_OBJECT'].'.'.$prop['LINKED_PROPERTY']))
						{
							setGlobal($prop['LINKED_OBJECT'].'.'.$prop['LINKED_PROPERTY'], $prop['CURRENT_VALUE_STRING'], array($this->name=>'0'));
						}
					}
					*/

					/*
					if ($prop['LINKED_OBJECT'] && $prop['LINKED_METHOD'])
					{ // && $old_value!=$prop['CURRENT_VALUE_STRING']
						$params=array();
						$params['TITLE']=$arRec['TITLE'];
						$params['VALUE']=$prop['CURRENT_VALUE_STRING'];
						$params['value']=$params['VALUE'];
						$params['port']=$prop['NUM'];
						$methodRes=callMethod($prop['LINKED_OBJECT'].'.'.$prop['LINKED_METHOD'], $params);

						if (is_string($methodRes)) {
							$ecmd=$methodRes;
						}

					}
					*/

					if (isset($value2))
					{
						$prop['CURRENT_VALUE_STRING2']=$value2;
						$prop['UPDATED']=date('d.m.Y H:i:s');
						//SQLUpdate('megadproperties', $prop);
						static::updateProperty($prop);

						/*
						if ($prop['LINKED_OBJECT2'] && $prop['LINKED_PROPERTY2'])
						{
							if ($old_value!=$prop['CURRENT_VALUE_STRING2'] || $prop['CURRENT_VALUE_STRING2']!=gg($prop['LINKED_OBJECT2'].'.'.$prop['LINKED_PROPERTY2']))
							{
								setGlobal($prop['LINKED_OBJECT2'].'.'.$prop['LINKED_PROPERTY2'], $prop['CURRENT_VALUE_STRING2'], array($this->name=>'0'));
							}
						}
						*/

						/*
						if ($prop['LINKED_OBJECT2'] && $prop['LINKED_METHOD2'])
						{ // && $old_value2!=$prop['CURRENT_VALUE_STRING2']
							$params=array();
							$params['TITLE']=$arRec['TITLE'];
							$params['VALUE']=$prop['CURRENT_VALUE_STRING2'];
							$params['value']=$params['VALUE'];
							$params['port']=$prop['NUM'];
							callMethod($prop['LINKED_OBJECT2'].'.'.$prop['LINKED_METHOD2'], $params);
						}
						*/
					}



				}
			}

			// internal temp sensor data
			if (isset($at))
			{
				//$prop=SQLSelectOne("SELECT * FROM megadproperties WHERE DEVICE_ID='".$arRec['ID']."' AND TYPE='100'");
				$prop = Tables\MegadPropertiesTable::getList(
					array(
						'filter' => array(
							'DEVICE_ID' => $arRec['ID'],
							'TYPE' => '100'
						),
						'limit' => 1
					)
				);
				if ($prop && isset($prop[0]))
				{
					$prop = $prop[0];
				}
				$value=$at;

				if ($prop['ID'])
				{
					$old_value=$prop['CURRENT_VALUE_STRING'];
					$prop['CURRENT_VALUE_STRING']=$value;
					$prop['UPDATED']=date('d.m.Y H:i:s');
					//SQLUpdate('megadproperties', $prop);
					static::updateProperty($prop);

					/*
					if ($prop['LINKED_OBJECT'] && $prop['LINKED_PROPERTY'])
					{
						if ($old_value!=$prop['CURRENT_VALUE_STRING'] || $prop['CURRENT_VALUE_STRING']!=gg($prop['LINKED_OBJECT'].'.'.$prop['LINKED_PROPERTY']))
						{
							setGlobal($prop['LINKED_OBJECT'].'.'.$prop['LINKED_PROPERTY'], $prop['CURRENT_VALUE_STRING'], array('megad'=>'0'));
						}
					}
					*/
				}
			}
		}


		if (isset($ecmd))
		{
			header_remove();
			header ('Content-Type:text/html;charset=windows-1251');

			if (preg_match('/(\d+):3/is', $ecmd, $m))
			{
				$ecmd=$m[1].':'.(int)$prop['CURRENT_VALUE_STRING'];
			}
			if (preg_match('/(\d+):4/is', $ecmd, $m))
			{
				if ((int)$prop['CURRENT_VALUE_STRING'])
				{
					$ecmd=$m[1].':0';
				}
				else
				{
					$ecmd=$m[1].':1';
				}
			}
			echo trim(utf2win($ecmd));

			$mega_id=$arRec['ID'];
			$code='';
			$code.='include_once(DIR_MODULES."megad/megad.class.php");';
			$code.='$mega=new megad();';
			$code.='$mega->readValues('.(int)$mega_id.');';
			//setTimeOut('mega_refresh_'.$mega_id, $code, 1);
		}
	}

	private static function readConfig ($deviceID)
	{
		//$record=SQLSelectOne("SELECT * FROM megaddevices WHERE ID='".(int)$id."'");
		$arRecord = Tables\MegadDevicesTable::getList(
			array(
				'filter' => array('ID'=>$deviceID),
				'limit' => 1
			)
		);
		if ($arRecord && isset($arRecord[0]))
		{
			$arRecord = $arRecord[0];
		}

		$cachedDir = Files::getCachedDir();
		$pluginsDir = CoreLib\Config::getConfig('KUZMAHOME_PLUGINS_ROOT');

		$url=$pluginsDir.'megad-cfg.php'
			.'?ip='.urlencode($arRecord['IP']).'&read-conf='.urlencode($cachedDir.'megad.cfg').'&p='.urlencode($arRecord['PASSWORD']);

		$data=Http::getURL($url, 0);

		if (preg_match('/OK/', $data))
		{
			$arRecord['CONFIG']=Files::loadFile($cachedDir.'megad.cfg');
			//SQLUpdate('megaddevices', $record);
			//unlink(ROOT.'cached/megad.cfg');
			static::updateDevice ($arRecord);

			//process config
			if (preg_match_all('/pn=(\d+)&(.+?)'."\n".'/is', $arRecord['CONFIG'], $m))
			{
				$total=count($m[2]);

				$total++;

				for($i=0;$i<$total;$i++)
				{
					$port=$m[1][$i];
					$line=$m[2][$i];
					$type='';

					if (preg_match('/pty=(\d+)/', $line, $m2))
					{
						$type=(int)$m2[1];
					}
					/*elseif (preg_match('/ecmd=/', $line))
					{
						$type=0;
					}
					else
					{
						$type=1;
					}

					if ($port==14 || $port==15)
					{
					 $type=2;
					}
					if ($i==16)
					{
					 $port=16;
					 $type=100;
					}
					*/


					if ($type!=='')
					{
						//$prop=SQLSelectOne("SELECT * FROM megadproperties WHERE DEVICE_ID='".$record['ID']."' AND NUM='".$port."'");
						$arProp = Tables\MegadPropertiesTable::getList(
							array(
								'filter' => array(
									'DEVICE_ID' => $arRecord['ID'],
									'NUM' => $port
								),
								'limit' => 1
							)
						);
						if ($arProp && isset($arProp[0]))
						{
							$arProp = $arProp[0];
						}

						$arProp['TYPE']=$type;
						$arProp['NUM']=$port;
						$arProp['DEVICE_ID']=$arRecord['ID'];


						if (preg_match('/ecmd=(.*?)\&/', $line, $m3)) {
							$arProp['ECMD']=$m3[1];
						}
						if (preg_match('/eth=(.*?)\&/', $line, $m3)) {
							$arProp['ETH']=$m3[1];
						}
						if (preg_match('/m=(\d+)/', $line, $m3)) {
							$arProp['MODE']=$m3[1];
						}
						if (preg_match('/d=(\d+)/', $line, $m3)) {
							$arProp['DEF']=$m3[1];
						}
						if (preg_match('/misc=(.*?)\&/', $line, $m3)) {
							$arProp['MISC']=$m3[1];
						}

						if (!$arProp['ID'])
						{
							//$prop['ID']=SQLInsert('megadproperties', $prop);
							$arProp['ID'] = Tables\MegadPropertiesTable::add(array('VALUES'=>$arProp))->getInsertId();
						}
						else
						{
							//SQLUpdate('megadproperties', $prop);
							static::updateProperty($arProp);
						}
					}
				}


				static::readValues($arRecord['ID']);
			}

		}
	}

	private static function updateDevice ($arDevice)
	{
		if (isset($arDevice['ID']))
		{
			$deviceID = $arDevice['ID'];
			unset($arDevice['ID']);

			return Tables\MegadDevicesTable::update($deviceID,array('VALUES'=>$arDevice));
		}

		return false;
	}

	private static function updateProperty ($arProperty)
	{
		if (isset($arProperty['ID']))
		{
			$propertyID = $arProperty['ID'];
			unset($arProperty['ID']);

			return Tables\MegadPropertiesTable::update($propertyID,array('VALUES'=>$arProperty));
		}

		return false;
	}

	private static function readValues ($deviceID)
	{
		//$record=SQLSelectOne("SELECT * FROM megaddevices WHERE ID='".(int)$id."'");
		$arRecord = Tables\MegadDevicesTable::getList(
			array(
				'filter' => array('ID'=>$deviceID),
				'limit' => 1
			)
		);
		if ($arRecord && isset($arRecord[0]))
		{
			$arRecord = $arRecord[0];
		}
		if (!$arRecord)
		{
			return false;
		}

		/*
		if ($all) {
			$stateData=$all;
		} else {
			$stateData=getURL('http://'.$record['IP'].'/'.$record['PASSWORD'].'/?cmd=all', 0);
		}*/
		$stateData=Http::getURL('http://'.$arRecord['IP'].'/'.$arRecord['PASSWORD'].'/?cmd=all', 0);

		//echo $stateData;exit;

		$states=explode(';', $stateData);

		$total=count($states);
		for($i=0;$i<$total;$i++)
		{
			//$prop=SQLSelectOne("SELECT * FROM megadproperties WHERE DEVICE_ID='".$record['ID']."' AND NUM='".$i."'");
			$arProp = Tables\MegadPropertiesTable::getList(
				array(
					'filter' => array(
						'DEVICE_ID' => $arRecord['ID'],
						'NUM' => $i
					),
					'limit' => 1
				)
			);
			if ($arProp && isset($arProp[0]))
			{
				$arProp = $arProp[0];
			}

			$type=intval($arProp['TYPE']);
			$mode=intval($arProp['MODE']);

			if (!$arProp['ID']) {
				continue;
			}

			//echo $type.' '.$states[$i]."<br/>";
			$old_value=$arProp['CURRENT_VALUE_STRING'];
			$old_value2=$arProp['CURRENT_VALUE_STRING2'];

			if ($states[$i]!=='')
			{
				if ($type==0)
				{
					$tmp=explode('/', $states[$i]);
					if ($tmp[0]=='ON')
					{
						$arProp['CURRENT_VALUE_STRING']=1;
					}
					else
					{
						$arProp['CURRENT_VALUE_STRING']=0;
					}
					if (isset($tmp[1]))
					{
						$arProp['CURRENT_VALUE_STRING2']=$tmp[1];
						$arProp['COUNTER']=$tmp[1];
					}
				}
				elseif ($type==1)
				{
					if($mode==1)
					{
						$arProp['CURRENT_VALUE_STRING']=intval($states[$i]);
					}
					else
					{
						if ($states[$i]=='ON')
						{
							$arProp['CURRENT_VALUE_STRING']=1;
						}
						else
						{
							$arProp['CURRENT_VALUE_STRING']=0;
						}
					}
				}
				elseif ($type==3 && preg_match('/temp:([\d\.]+)\/hum:([\d\.]+)/', $states[$i], $m))
				{
					$arProp['CURRENT_VALUE_STRING']=$m[1];
					$arProp['CURRENT_VALUE_STRING2']=$m[2];
				}
				else
				{
					$tmp=explode('/', $states[$i]);
					$tmp[0]=str_replace("temp:", "", $tmp[0]);
					$tmp[0]=str_replace("hum:", "", $tmp[0]);
					$arProp['CURRENT_VALUE_STRING']=$tmp[0];
					if (isset($tmp[1]))
					{
						$arProp['CURRENT_VALUE_STRING2']=$tmp[1];
					}
				}
			}

			$arProp['UPDATED']=date('d.m.Y H:i:s');
			//SQLUpdate('megadproperties', $prop);
			static::updateProperty($arProp);

			//echo $stateData;exit;


			/*
			if ($arProp['LINKED_OBJECT'] && $arProp['LINKED_PROPERTY'])
			{
				if ($old_value!=$arProp['CURRENT_VALUE_STRING'] || $arProp['CURRENT_VALUE_STRING']!=gg($arProp['LINKED_OBJECT'].'.'.$arProp['LINKED_PROPERTY']))
				{
					setGlobal($arProp['LINKED_OBJECT'].'.'.$arProp['LINKED_PROPERTY'], $arProp['CURRENT_VALUE_STRING'], array('megad'=>'0'));
				}
			}
			*/

			/*
			if ($arProp['LINKED_OBJECT'] && $arProp['LINKED_METHOD'] && ($old_value!=$arProp['CURRENT_VALUE_STRING'])) {
				$params=array();
				$params['TITLE']=$arRecord['TITLE'];
				$params['VALUE']=$arProp['CURRENT_VALUE_STRING'];
				$params['value']=$params['VALUE'];
				$params['port']=$i;
				callMethod($arProp['LINKED_OBJECT'].'.'.$arProp['LINKED_METHOD'], $params);
			}
			*/

			/*
			if ($arProp['LINKED_OBJECT2'] && $arProp['LINKED_PROPERTY2'])
			{
				if ($old_value2!=$arProp['CURRENT_VALUE_STRING2'] || $arProp['CURRENT_VALUE_STRING2']!=gg($arProp['LINKED_OBJECT2'].'.'.$arProp['LINKED_PROPERTY2']))
				{
					setGlobal($arProp['LINKED_OBJECT2'].'.'.$arProp['LINKED_PROPERTY2'], $arProp['CURRENT_VALUE_STRING2'], array('megad'=>'0'));
				}
			}
			*/

			/*
			if ($arProp['LINKED_OBJECT2'] && $arProp['LINKED_METHOD2'] && ($old_value2!=$arProp['CURRENT_VALUE_STRING2'])) {
				$params=array();
				$params['TITLE']=$arRecord['TITLE'];
				$params['VALUE']=$arProp['CURRENT_VALUE_STRING2'];
				$params['value']=$params['VALUE'];
				$params['port']=$i;
				callMethod($arProp['LINKED_OBJECT2'].'.'.$arProp['LINKED_METHOD2'], $params);
			}
			*/

		}


		//internal temp sensor data
		//$arProp=SQLSelectOne("SELECT * FROM megadproperties WHERE DEVICE_ID='".$arRecord['ID']."' AND TYPE='100'");
		$arProp = Tables\MegadPropertiesTable::getList(
			array(
				'filter' => array(
					'DEVICE_ID' => $arRecord['ID'],
					'TYPE' => '100'
				),
				'limit' => 1
			)
		);
		if ($arProp && isset($arProp[0]))
		{
			$arProp = $arProp[0];
		}
		if ($arProp['ID'])
		{
			$stateData=Http::getURL('http://'.$arRecord['IP'].'/'.$arRecord['PASSWORD'].'/?tget=1', 0);
			$old_value=$arProp['CURRENT_VALUE_STRING'];
			if ($stateData!='')
			{
				$arProp['CURRENT_VALUE_STRING']=$stateData;
				$arProp['UPDATED']=date('Y-m-d H:i:s');
				//SQLUpdate('megadproperties', $prop);
				static::updateProperty($arProp);

				/*
				if ($arProp['LINKED_OBJECT'] && $arProp['LINKED_PROPERTY'])
				{
					if ($old_value!=$arProp['CURRENT_VALUE_STRING'] || $arProp['CURRENT_VALUE_STRING']!=gg($arProp['LINKED_OBJECT'].'.'.$arProp['LINKED_PROPERTY']))
					{
						setGlobal($arProp['LINKED_OBJECT'].'.'.$arProp['LINKED_PROPERTY'], $arProp['CURRENT_VALUE_STRING'], array('megad'=>'0'));
					}
				}
				*/

			}
		}
	}
}