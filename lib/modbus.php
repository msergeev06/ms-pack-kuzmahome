<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Entity;

class Modbus
{
	/**
	 * @const string options for 'REQUEST_TYPE' |FC23=FC23 Read/Write multiple registers
	 */
	const DEF_REQUEST_TYPE_OPTIONS = 'FC1=FC1 Read coils|FC2=FC2 Read input discretes|FC3=FC3 Read holding registers|FC4=FC4 Read holding input registers|FC5=FC5 Write single coil|FC6=FC6 Write single register|FC15=FC15 Write multiple coils|FC16=FC16 Write multiple registers';

	/**
	 * @const string options for 'RESPONSE_CONVERT'
	 */
	const DEF_RESPONSE_CONVERT_OPTIONS = '0=None (bytes)|r2f=REAL to Float|d2i=DINT to integer|dw2i=DWORD to integer|i2i=INT to integer|w2i=WORD to integer|s=String';

	public static function readAll() {
		//$devices=SQLSelect("SELECT ID FROM modbusdevices WHERE CHECK_NEXT<NOW()");
		$arDevices = Tables\ModbusDevicesTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array(
					'<CHECK_NEXT' => date('d.m.Y H:i:s')
				)
			)
		);
		if ($arDevices)
		{
			foreach ($arDevices as $device)
			{
				static::poll_device($device['ID']);
			}

		}
	}

	private static function propertySetHandle ($object, $property, $value) {
		//$arModbusDevices=SQLSelect("SELECT ID FROM modbusdevices WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
		$arModbusDevices = Tables\ModbusDevicesTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array(
					'LINKED_OBJECT' => $object,
					'LINKED_PROPERTY' => $property
				)
			)
		);
		if ($arModbusDevices)
		{
			foreach ($arModbusDevices as $device)
			{
				static::poll_device($device['ID']);
			}
		}
	}

	private static function poll_device($id)
	{
		//$rec=SQLSelectOne("SELECT * FROM modbusdevices WHERE ID='".(int)$id."'");
		$rec = Tables\ModbusDevicesTable::getList(
			array(
				'filter' => array(
					'ID' => intval($id)
				),
				'limit' => 1
			)
		);
		if ($rec && isset($rec[0]))
		{
			$rec = $rec[0];
		}
		if (!$rec['ID']) {
			return;
		}

		$arUpdate = array();
		$arUpdate['CHECK_LATEST'] = $rec['CHECK_LATEST']=date('d.m.Y H:i:s');
		$arUpdate['CHECK_NEXT'] = $rec['CHECK_NEXT']=date('d.m.Y H:i:s', (time()+intval($rec['POLLPERIOD'])));
		//SQLUpdate('modbusdevices', $rec);
		Tables\ModbusDevicesTable::update($rec['ID'],array("VALUES"=>$arUpdate));

		if (
			$rec['LINKED_OBJECT'] && $rec['LINKED_PROPERTY'] &&
			($rec['REQUEST_TYPE']=='FC5' || $rec['REQUEST_TYPE']=='FC6' || $rec['REQUEST_TYPE']=='FC15' || $rec['REQUEST_TYPE']=='FC16' || $rec['REQUEST_TYPE']=='FC23')
		)
		{
			$rec['DATA']=Objects::getGlobal($rec['LINKED_OBJECT'].'.'.$rec['LINKED_PROPERTY']);
		}


		//require_once dirname(__FILE__) . '/ModbusMaster.php';
		$modbus = new Entity\ModbusMaster($rec['HOST'], $rec['PROTOCOL']);
		if ($rec['PORT'])
		{
			$modbus->port=$rec['PORT'];
		}

		$recData = array();
		if ($rec['REQUEST_TYPE']=='FC1')
		{
			//FC1 Read coils
			try
			{
				$recData = $modbus->readCoils($rec['DEVICE_ID'], $rec['REQUEST_START'], $rec['REQUEST_TOTAL']);
				if (is_array($recData))
				{
					foreach($recData as $k=>$v)
						$recData[$k]=(int)$v;
				}
			}
			catch (\Exception $e)
			{
				// Print error information if any
				$rec['LOG']=date('d.m.Y H:i:s')." FC1 Error: $modbus $e\n".$rec['LOG'];
			}
		}
		elseif ($rec['REQUEST_TYPE']=='FC2')
		{
			//FC2 Read input discretes
			try
			{
				$recData = $modbus->readInputDiscretes($rec['DEVICE_ID'], $rec['REQUEST_START'], $rec['REQUEST_TOTAL']);
				if (is_array($recData))
				{
					foreach($recData as $k=>$v)
						$recData[$k]=(int)$v;
				}
			}
			catch (\Exception $e)
			{
				// Print error information if any
				$rec['LOG']=date('d.m.Y H:i:s')." FC2 Error: $modbus $e\n".$rec['LOG'];
			}
		}
		elseif ($rec['REQUEST_TYPE']=='FC3')
		{
			//FC3 Read holding registers
			try
			{
				$recData = $modbus->readMultipleRegisters($rec['DEVICE_ID'], $rec['REQUEST_START'], $rec['REQUEST_TOTAL']);
			}
			catch (\Exception $e)
			{
				// Print error information if any
				$rec['LOG']=date('d.m.Y H:i:s')." FC3 Error: $modbus $e\n".$rec['LOG'];
			}
		}
		elseif ($rec['REQUEST_TYPE']=='FC4')
		{
			//FC4 Read holding input registers
			try
			{
				$recData = $modbus->readMultipleInputRegisters($rec['DEVICE_ID'], $rec['REQUEST_START'], $rec['REQUEST_TOTAL']);
			}
			catch (\Exception $e)
			{
				// Print error information if any
				$rec['LOG']=date('d.m.Y H:i:s')." FC4 Error: $modbus $e\n".$rec['LOG'];
			}
		}
		elseif ($rec['REQUEST_TYPE']=='FC5')
		{
			//FC5 Write single coil
			if ((int)$rec['DATA'])
			{
				$data_set=array(TRUE);
			} else
			{
				$data_set=array(FALSE);
			}
			try
			{
				$modbus->writeSingleCoil($rec['DEVICE_ID'], $rec['REQUEST_START'], $data_set);
			}
			catch (\Exception $e)
			{
				$rec['LOG']=date('d.m.Y H:i:s')." FC5 Error: $modbus $e\n".$rec['LOG'];
			}
		}
		elseif ($rec['REQUEST_TYPE']=='FC6')
		{
			//FC6 Write single register
			try
			{
				$data_set=array((int)$rec['DATA']);
				if ($rec['RESPONSE_CONVERT']=='r2f')
				{
					$dataTypes = array("REAL");
				} elseif ($rec['RESPONSE_CONVERT']=='d2i' || $rec['RESPONSE_CONVERT']=='dw2i')
				{
					$dataTypes = array("DINT");
				} else
				{
					$dataTypes = array("INT");
				}
				$recData = $modbus->writeSingleRegister($rec['DEVICE_ID'], $rec['REQUEST_START'], $data_set, $dataTypes);
			}
			catch (\Exception $e)
			{
				// Print error information if any
				$rec['LOG']=date('d.m.Y H:i:s')." FC6 Error: $modbus $e\n".$rec['LOG'];
			}
		}
		elseif ($rec['REQUEST_TYPE']=='FC15')
		{
			//FC15 Write multiple coils
			$data_set=explode(',', $rec['DATA']);
			foreach($data_set as $k=>$v)
			{
				$data_set[$k]=(bool)$v;
			}
			try
			{
				$modbus->writeMultipleCoils($rec['DEVICE_ID'], $rec['REQUEST_START'], $data_set);
			}
			catch (\Exception $e)
			{
				$rec['LOG']=date('d.m.Y H:i:s')." FC15 Error: $modbus $e\n".$rec['LOG'];
			}

		}
		elseif ($rec['REQUEST_TYPE']=='FC16')
		{
			//FC16 Write multiple registers
			try
			{
				$data_set=explode(',', $rec['DATA']);
				$dataTypes=array();
				foreach($data_set as $k=>$v)
				{
					if ($rec['RESPONSE_CONVERT']=='r2f')
					{
						$dataTypes[] = "REAL";
						$data_set[$k]=(float)$v;
					}
					elseif ($rec['RESPONSE_CONVERT']=='d2i' || $rec['RESPONSE_CONVERT']=='dw2i')
					{
						$dataTypes[] = "DINT";
						$data_set[$k]=(int)$v;
					}
					else
					{
						$data_set[$k]=(int)$v;
						$dataTypes[] = "INT";
					}
				}
				$recData = $modbus->writeMultipleRegister($rec['DEVICE_ID'], $rec['REQUEST_START'], $data_set, $dataTypes);
			}
			catch (\Exception $e)
			{
				// Print error information if any
				$rec['LOG']=date('d.m.Y H:i:s')." FC16 Error: $modbus $e\n".$rec['LOG'];
			}
		}
		elseif ($rec['REQUEST_TYPE']=='FC23')
		{
			//FC23 Read/Write multiple registers
			//TO-DO
		}

		//echo $rec['LOG'];exit;



		if ($rec['REQUEST_TYPE']=='FC1' || $rec['REQUEST_TYPE']=='FC2' || $rec['REQUEST_TYPE']=='FC3' || $rec['REQUEST_TYPE']=='FC4' && is_array($recData))
		{
			// PROCESS RESPONSE

			if ($rec['RESPONSE_CONVERT']=='r2f')
			{
				//REAL to Float
				$values = array_chunk($recData, 4);
				$recData=array();
				foreach($values as $bytes)
				{
					echo $recData[]=PhpType::bytes2float($bytes);
				}
			}
			elseif ($rec['RESPONSE_CONVERT']=='d2i')
			{
				//DINT to integer
				$values = array_chunk($recData, 4);
				$recData=array();
				foreach($values as $bytes)
				{
					echo $recData[]=PhpType::bytes2signedInt($bytes);
				}
			}
			elseif ($rec['RESPONSE_CONVERT']=='dw2i')
			{
				//DWORD to integer
				$values = array_chunk($recData, 4);
				$recData=array();
				foreach($values as $bytes)
				{
					$recData[]=PhpType::bytes2unsignedInt($bytes);
				}
			}
			elseif ($rec['RESPONSE_CONVERT']=='i2i')
			{
				//INT to integer
				$values = array_chunk($recData, 2);
				$recData=array();
				foreach($values as $bytes)
				{
					$recData[]=PhpType::bytes2signedInt($bytes);
				}
			}
			elseif ($rec['RESPONSE_CONVERT']=='w2i')
			{
				//WORD to integer
				$values = array_chunk($recData, 2);
				$recData=array();
				foreach($values as $bytes)
				{
					$recData[]=PhpType::bytes2unsignedInt($bytes);
				}
			}
			elseif ($rec['RESPONSE_CONVERT']=='s')
			{
				//String
				$recData=array(PhpType::bytes2string($recData));
			}
			else
			{
				//
			}
			$result=implode(',', $recData);
			if ($result && $result!=$rec['DATA']) {
				$rec['LOG']=date('d.m.Y H:i:s')." ".$result."\n".$rec['LOG'];
			}
			$rec['DATA']=$result;
			$recID = $rec['ID'];
			unset($rec['ID']);
			//SQLUpdate('modbusdevices', $rec);
			Tables\ModbusDevicesTable::update($recID,array("VALUES"=>$rec));
			if ($rec['LINKED_OBJECT'] && $rec['LINKED_PROPERTY']) {
				Objects::setGlobal($rec['LINKED_OBJECT'].'.'.$rec['LINKED_PROPERTY'], $rec['DATA'], array('modbus'=>'0'));
			}

		}
		else
		{
			$recID = $rec['ID'];
			unset($rec['ID']);
			//SQLUpdate('modbusdevices', $rec);
			Tables\ModbusDevicesTable::update($recID,array("VALUES"=>$rec));
		}
	}

	/**
	 * modbusdevices search
	 *
	 * @access public
	 */
	private static function search_modbusdevices(&$out)
	{
		//require(DIR_MODULES.$this->name.'/modbusdevices_search.inc.php');
	}
	/**
	 * modbusdevices edit/add
	 *
	 * @access public
	 */
	private static function edit_modbusdevices(&$out, $id)
	{
		//require(DIR_MODULES.$this->name.'/modbusdevices_edit.inc.php');
	}
	/**
	 * modbusdevices delete record
	 *
	 * @access public
	 */
	private static function delete_modbusdevices($id)
	{
		//$rec=SQLSelectOne("SELECT * FROM modbusdevices WHERE ID='$id'");
		// some action for related tables
		//SQLExec("DELETE FROM modbusdevices WHERE ID='".$rec['ID']."'");
	}

}