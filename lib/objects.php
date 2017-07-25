<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Entity\Query;
use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Entity;
use MSergeev\Packages\Kuzmahome\Tables;

class Objects
{
	private static $systemObject = 'System';
	public static $usersClass = 'Users';
	public static $roomsClass = 'Rooms';

	public static function addClass ($class_name, $parent_class = 0, $description='')
	{
		if ($parent_class != 0)
		{
			$parent_class_id = static::addClass($parent_class);
		}
		else
		{
			$parent_class_id = 0;
		}

		$arRes = Tables\ClassesTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array('TITLE'=>$class_name),
				'limit' => 1
			)
		);
		if ($arRes && isset($arRes[0]))
		{
			$arRes = $arRes[0];
		}

		if (!$arRes)
		{
			$arClass = array();

			$arClass['TITLE']     = $class_name;
			$arClass['PARENT_ID'] = intval($parent_class_id);
			if (strlen($description)>0)
			{
				$arClass['DESCRIPTION'] = $description;
			}

			return Tables\ClassesTable::add(array("VALUES"=>$arClass))->getInsertId();
		}
		else
		{
			return $arRes['ID'];
		}
	}

	public static function addClassMethod ($class_name, $method_name, $code = '', $description='')
	{
		$classID = static::addClass($class_name);

		if ($classID)
		{
			$arRes = Tables\MethodsTable::getList(
				array(
					'filter' => array(
						'CLASS_ID' => $classID,
						'TITLE' => $method_name,
						'OBJECT_ID' => NULL
					),
					'limit' => 1
				)
			);
			if ($arRes && isset($arRes[0]))
			{
				$arRes = $arRes[0];
			}
			if (!$arRes)
			{
				$arMethod = array();

				$arMethod['CLASS_ID']  = $classID;
				$arMethod['CODE']      = $code;
				$arMethod['TITLE']     = $method_name;
				if (strlen($description)>0)
				{
					$arMethod['DESCRIPTION'] = $description;
				}

				return Tables\MethodsTable::add(array("VALUES"=>$arMethod))->getInsertId();
			}
			else
			{
				if ($code != '' && $arRes['CODE'] != $code)
				{
					$arMethod = array();

					$arMethod['CODE'] = $code;

					Tables\MethodsTable::update($arRes['ID'],array("VALUES"=>$arMethod));
				}

				return $arRes['ID'];
			}
		}

		return false;
	}

	public static function addClassProperty ($class_name, $property_name, $keep_history = 0, $description=null)
	{
		$classID = self::addClass ($class_name);

		$arRes = Tables\PropertiesTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array(
					'TITLE' => $property_name,
					'CLASS_ID' => $classID,
					'OBJECT_ID' => NULL
				),
				'limit' => 1
			)
		);
		if ($arRes && isset($arRes[0]))
		{
			$arRes = $arRes[0];
		}
		if (!isset($arRes['ID']))
		{
			$arProp = array();

			$arProp['CLASS_ID']     = $classID;
			$arProp['TITLE']        = $property_name;
			$arProp['KEEP_HISTORY'] = $keep_history;
			if (!is_null($description))
			{
				$arProp['DESCRIPTION'] = $description;
			}

			return Tables\PropertiesTable::add(array("VALUES"=>$arProp))->getInsertId();
		}
		else
		{
			return $arRes['ID'];
		}
	}

	public static function addClassObject ($class_name, $object_name, $description='', $roomID=0)
	{
		$classID = self::addClass($class_name);

		$arRes = Tables\ObjectsTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array('TITLE'=>$object_name),
				'limit' => 1
			)
		);
		if ($arRes && isset($arRes[0]))
		{
			$arRes = $arRes[0];
		}

		if (isset($arRes['ID']))
		{
			return $arRes['ID'];
		}
		else
		{
			$arObject = array();

			$arObject['TITLE']    = $object_name;
			$arObject['CLASS_ID'] = $classID;
			if (strlen($description)>0)
			{
				$arObject['DESCRIPTION'] = $description;
			}
			if (intval($roomID)>0)
			{
				$arObject['ROOM_ID'] = intval($roomID);
			}

			return Tables\ObjectsTable::add(array("VALUES"=>$arObject))->getInsertId();
		}
	}

	public static function getValueIdByName($object_name, $property)
	{
		$arRes = Tables\PropertyValuesTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array(
					'PROPERTY_NAME' => $object_name . '.' . $property
				)
			)
		);
		if ($arRes && isset($arRes[0]))
		{
			$arRes = $arRes[0];
		}
		if (!isset($arRes['ID']))
		{
			$object = static::getObject($object_name);

			if ($object instanceof Entity\Object)
			{
				$property_id = $object->getPropertyByName($property /*, $object->classID, $object->id*/);

				//$sqlQuery = "SELECT ID FROM pvalues WHERE PROPERTY_ID = " . (int)$property_id . " AND OBJECT_ID   = " . (int)$object->id;
				//$value = SQLSelectOne($sqlQuery);
				$value = Tables\PropertyValuesTable::getList(
					array(
						'select' => array('ID'),
						'filter' => array(
							'PROPERTY_ID' => intval($property_id),
							'OBJECT_ID' => intval($object->id)
						),
						'limit' => 1
					)
				);
				if ($value && isset($value[0]))
				{
					$value = $value[0];
				}

				if (!$value['ID'] && $property_id)
				{
					$value = array();

					$value['PROPERTY_ID']   = $property_id;
					$value['OBJECT_ID']     = $object->id;
					$value['PROPERTY_NAME'] = $object_name . '.' . $property;
					//$value['ID']            = SQLInsert('pvalues', $value);
					$value['ID'] = Tables\PropertyValuesTable::add(array("VALUES"=>$value))->getInsertId();
				}

				return intval($value['ID']);
			}
			else
			{
				return false;
			}
		}
		else
		{
			return intval($arRes['ID']);
		}
	}

	public static function addLinkedProperty($object, $property, $module)
	{
		$value = Tables\PropertyValuesTable::getList(
			array(
				'filter' => array(
					'ID' => self::getValueIdByName($object, $property)
				),
				'limit' => 1
			)
		);
		if ($value && isset($value[0]))
		{
			$value = $value[0];
		}
		if (isset($value['ID']))
		{
			if (!$value['LINKED_MODULES'])
			{
				$tmp = array();
			}
			else
			{
				$tmp = explode(',', $value['LINKED_MODULES']);
			}

			if (!in_array($module, $tmp))
			{
				$tmp[] = $module;

				$value['LINKED_MODULES'] = implode(',', $tmp);

				Tables\PropertyValuesTable::update(
					$value['ID'],
					array(
						"VALUES" => array(
							'LINKED_MODULES' => $value['LINKED_MODULES']
						)
					)
				);
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	public static function removeLinkedProperty($object, $property, $module)
	{
		$value = Tables\PropertyValuesTable::getList(
			array(
				'filter' => array(
					'ID' => self::getValueIdByName($object, $property)
				),
				'limit' => 1
			)
		);
		if ($value && isset($value[0]))
		{
			$value = $value[0];
		}

		if (isset($value['ID']))
		{
			if (!$value['LINKED_MODULES'])
			{
				$tmp = array();
			}
			else
			{
				$tmp = explode(',', $value['LINKED_MODULES']);
			}

			if (in_array($module, $tmp))
			{
				$total = count($tmp);
				$res   = array();

				for ($i = 0; $i < $total; $i++)
				{
					if ($tmp[$i] != $module)
					{
						$res[] = $tmp[$i];
					}
				}

				$tmp = $res;

				$value['LINKED_MODULES'] = implode(',', $tmp);

				Tables\PropertyValuesTable::update(
					$value['ID'],
					array(
						"VALUES" => array(
							'LINKED_MODULES' => $value['LINKED_MODULES']
						)
					)
				);
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	public static function getObject ($name)
	{
		//$qry = '1';

		if (preg_match('/^(.+?)\.(.+?)$/', $name, $m))
		{
			$class_name  = $m[1];
			$object_name = $m[2];

			$query = new Query('select');
			$sqlHelperObjects = new CoreLib\SqlHelper(Tables\ObjectsTable::getTableName());
			$sqlHelperClasses = new CoreLib\SqlHelper(Tables\ClassesTable::getTableName());
			$sql = "SELECT\n\t"
				.$sqlHelperObjects->wrapFieldQuotes('ID')."\nFROM\n\t"
				.$sqlHelperObjects->wrapTableQuotes()."\n"
				."LEFT JOIN ".$sqlHelperClasses->wrapTableQuotes()." ON "
				.$sqlHelperObjects->wrapFieldQuotes('CLASS_ID')." = ".$sqlHelperClasses->wrapFieldQuotes('ID')."\n"
				."WHERE\n\t".$sqlHelperObjects->wrapFieldQuotes('TITLE')." LIKE '" . $object_name . "' AND\n\t"
				.$sqlHelperClasses->wrapFieldQuotes('TITLE')." LIKE '" . $class_name . "'\nLIMIT 1";
			$query->setQueryBuildParts($sql);
			$arRes = $query->exec()->fetch();
		}
		else
		{
			$arRes = Tables\ObjectsTable::getList(
				array(
					'select' => array('ID'),
					'filter' => array('TITLE'=>$name),
					'limit' => 1
				)
			);
			if ($arRes && $arRes[0])
			{
				$arRes = $arRes[0];
			}
		}

		if (isset($arRes['ID']))
		{
			//include_once(DIR_MODULES . 'objects/objects.class.php');

			$obj = new Entity\Object();

			$obj->loadObject($arRes['ID']);

			return $obj;
		}

		return false;
	}

	public static function getObjectsByProperty ($property_name, $condition='', $condition_value='')
	{
		//$pRecs=SQLSelect("SELECT ID FROM properties WHERE TITLE LIKE '".DBSafe($property_name)."'");
		$pRecs = Tables\PropertiesTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array(
					'TITLE' => $property_name
				)
			)
		);
		$total=count($pRecs);
		if (!$total)
		{
			return 0;
		}
		$found=array();
		for($i=0;$i<$total;$i++)
		{
			//$pValues=SQLSelect("SELECT objects.TITLE, VALUE FROM pvalues LEFT JOIN objects ON pvalues.OBJECT_ID=objects.ID WHERE PROPERTY_ID='".$pRecs[$i]['ID']."'");
			$query = new Query('select');
			$sqlHelpObj = new CoreLib\SqlHelper(Tables\ObjectsTable::getTableName());
			$sqlHelpVal = new CoreLib\SqlHelper(Tables\PropertyValuesTable::getTableName());
			$sql = "SELECT\n\t"
				.$sqlHelpObj->wrapFieldQuotes('TITLE').", "
				.$sqlHelpVal->wrapFieldQuotes('VALUE')."\nFROM\n\t"
				.$sqlHelpVal->wrapTableQuotes()."\n"
				."LEFT JOIN "
				.$sqlHelpObj->wrapTableQuotes()." ON "
				.$sqlHelpVal->wrapFieldQuotes('OBJECT_ID')."="
				.$sqlHelpObj->wrapFieldQuotes('ID')."\nWHERE\n\t"
				.$sqlHelpVal->wrapFieldQuotes('PROPERTY_ID')."='".$pRecs[$i]['ID']."'";
			$query->setQueryBuildParts($sql);
			$res = $query->exec();
			$pValues = array();
			while ($ar_res = $res->fetch())
			{
				$pValues[] = $ar_res;
			}

			$totalv=count($pValues);
			for($iv=0;$iv<$totalv;$iv++)
			{
				$v=$pValues[$iv]['VALUE'];
				if (!$condition)
				{
					$found[$pValues[$iv]['TITLE']]=1;
				}
				elseif (($condition=='=' || $condition=='==') && ($v==$condition_value))
				{
					$found[$pValues[$iv]['TITLE']]=1;
				}
				elseif (($condition=='>=') && ($v>=$condition_value))
				{
					$found[$pValues[$iv]['TITLE']]=1;
				}
				elseif (($condition=='>') && ($v>$condition_value))
				{
					$found[$pValues[$iv]['TITLE']]=1;
				}
				elseif (($condition=='<=') && ($v<=$condition_value))
				{
					$found[$pValues[$iv]['TITLE']]=1;
				}
				elseif (($condition=='<') && ($v<$condition_value))
				{
					$found[$pValues[$iv]['TITLE']]=1;
				}
				elseif (($condition=='<>' || $condition=='!=') && ($v!=$condition_value))
				{
					$found[$pValues[$iv]['TITLE']]=1;
				}
			}
		}

		$res=array();
		foreach($found as $k=>$v)
		{
			$res[]=$k;
		}
		return $res;
	}

	public static function getObjectsByClass($class_name)
	{
		//$sqlQuery = "SELECT ID FROM classes WHERE (TITLE LIKE '" . DBSafe(trim($class_name)) . "' OR ID = " . (int)$class_name . " )";
		//$class_record = SQLSelectOne($sqlQuery);

		$query = new Query('select');
		$sqlHelp = new CoreLib\SqlHelper(Tables\ClassesTable::getTableName());
		$sql = "SELECT\n\t"
			.$sqlHelp->wrapFieldQuotes('ID')."\nFROM\n\t"
			.$sqlHelp->wrapTableQuotes()."\nWHERE\n\t("
			.$sqlHelp->wrapFieldQuotes('TITLE')." LIKE '" . trim($class_name) . "' OR "
			.$sqlHelp->wrapFieldQuotes('ID')." = " . intval($class_name) . ")\nLIMIT 1";
		$query->setQueryBuildParts($sql);
		$class_record = $query->exec()->fetch();

		if (!$class_record['ID'])
		{
			return 0;
		}

		//$sqlQuery = "SELECT ID, TITLE FROM objects WHERE CLASS_ID = '" . $class_record['ID'] . "'";
		//$objects = SQLSelect($sqlQuery);

		$objects = Tables\ObjectsTable::getList(
			array(
				'select' => array('ID','TITLE'),
				'filter' => array(
					'CLASS_ID' => intval($class_record['ID'])
				)
			)
		);

		//$sqlQuery = "SELECT ID, TITLE FROM classes WHERE PARENT_ID = '" . $class_record['ID'] . "'";
		//$sub_classes = SQLSelect($sqlQuery);

		$sub_classes = Tables\ClassesTable::getList(
			array(
				'select' => array('ID','TITLE'),
				'filter' => array(
					'PARENT_ID' => intval($class_record['ID'])
				)
			)
		);

		if (isset($sub_classes[0]['ID']))
		{
			$total = count($sub_classes);

			for ($i = 0; $i < $total; $i++)
			{
				$sub_objects = static::getObjectsByClass($sub_classes[$i]['TITLE']);

				if (isset($sub_objects[0]['ID']))
				{
					foreach ($sub_objects as $obj)
					{
						$objects[] = $obj;
					}
				}
			}
		}

		/*
		   $total=count($objects);
		   for($i=0;$i<$total;$i++) {
		   $objects[$i]=getObject($objects[$i]['TITLE'])
		   }
			*/

		return $objects;
	}

	public static function getGlobal ($varname)
	{
		static::parseVarname($varname,$object_name);

		//$cached_name  = 'MJD:' . $object_name . '.' . $varname;
		//$cached_value = checkFromCache($cached_name);

		//if ($cached_value !== false)
		//{
		//	return $cached_value;
		//}

		$obj = static::getObject($object_name);

		if ($obj instanceof Entity\Object)
		{
			$value = $obj->getProperty($varname);
			//saveToCache($cached_name, $value);

			return $value;
		}
		else
		{
			return false;
		}
	}

	public static function getHistoryValueId ($varname)
	{
		static::parseVarname($varname, $object_name);

		// Get object
		$obj = static::getObject($object_name);
		$noObj = true;
		if ($obj instanceof Entity\Object)
		{
			$noObj = false;
		}
		if ($noObj) return false;

		// Get property
		$prop_id = $obj->getPropertyByName($varname /*, $obj->class_id, $obj->id*/);
		if ($prop_id == false)
		{
			return false;
		}

		//$rec=SQLSelectOne("SELECT * FROM pvalues WHERE PROPERTY_ID='".(int)$prop_id."' AND OBJECT_ID='".(int)$obj->id."'");
		$rec = Tables\PropertyValuesTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array(
					'PROPERTY_ID' => intval($prop_id),
					'OBJECT_ID' => intval($obj->id)
				),
				'limit' => 1
			)
		);
		if ($rec && isset($rec[0]))
		{
			$rec = $rec[0];
		}

		if (!isset($rec['ID']))
		{
			return false;
		}

		return $rec['ID'];
	}

	public static function getHistory ($varname, $start_time = 0, $stop_time = 0) {
		if ($start_time <= 0)
		{
			$start_time = (time() + $start_time);
		}
		if ($stop_time  <= 0)
		{
			$stop_time  = (time() + $stop_time);
		}

		// Get hist val id
		$id = static::getHistoryValueId($varname);

		// Get data
		//return SQLSelect("SELECT VALUE, ADDED FROM phistory WHERE VALUE_ID='".$id."' AND ADDED>=('".date('Y-m-d H:i:s', $start_time)."') AND ADDED<=('".date('Y-m-d H:i:s', $stop_time)."')");
		return Tables\PropertyHistoryTable::getList(
			array(
				'select' => array('VALUE','ADDED'),
				'filter' => array(
					'VALUE_ID' => intval($id),
					'>=ADDED' => date('d.m.Y H:i:s', $start_time),
					'<=ADDED' => date('d.m.Y H:i:s', $stop_time)
				)
			)
		);
	}

	public static function getHistoryMin ($varname, $start_time = 0, $stop_time = 0)
	{
		return static::getHistoryFunc($varname, $start_time, $stop_time, 'MIN');
	}

	public static function getHistoryMax($varname, $start_time = 0, $stop_time = 0)
	{
		return static::getHistoryFunc($varname, $start_time, $stop_time, 'MAX');
	}

	public static function getHistoryCount($varname, $start_time = 0, $stop_time = 0)
	{
		return static::getHistoryFunc($varname, $start_time, $stop_time, 'COUNT');
	}

	public static function getHistorySum($varname, $start_time, $stop_time = 0)
	{
		return static::getHistoryFunc($varname, $start_time, $stop_time, 'SUM');
	}

	public static function getHistoryAvg($varname, $start_time, $stop_time = 0)
	{
		return static::getHistoryFunc($varname, $start_time, $stop_time, 'AVG');
	}

	public static function getHistoryValue($varname, $time = 0, $nerest = false)
	{
		if ($time <= 0)
		{
			$time = (time() + $time);
		}

		// Get hist val id
		$id = static::getHistoryValueId($varname);

		// Get val before
		//$val1 = SQLSelectOne("SELECT VALUE, UNIX_TIMESTAMP(ADDED) AS ADDED FROM phistory WHERE VALUE_ID='".$id."' AND ADDED<=('".date('Y-m-d H:i:s', $time)."') ORDER BY ADDED DESC LIMIT 1");
		$query = new Query('select');
		$sqlHelp = new CoreLib\SqlHelper(Tables\PropertyHistoryTable::getTableName());
		$sql = "SELECT\n\t"
			.$sqlHelp->wrapFieldQuotes('VALUE').", UNIX_TIMESTAMP("
			.$sqlHelp->wrapFieldQuotes('ADDED').") AS "
			.$sqlHelp->wrapQuotes('ADDED')."\nFROM\n\t"
			.$sqlHelp->wrapTableQuotes()."\nWHERE\n\t"
			.$sqlHelp->wrapFieldQuotes('VALUE_ID')."='".intval($id)."' AND\n\t"
			.$sqlHelp->wrapFieldQuotes('ADDED')."<=('".date('Y-m-d H:i:s', $time)."')\n"
			."ORDER BY "
			.$sqlHelp->wrapFieldQuotes('ADDED')." DESC\nLIMIT 1";
		$query->setQueryBuildParts($sql);
		$val1 = $query->exec()->fetch();

		// Get val after
		//$val2 = SQLSelectOne("SELECT VALUE, UNIX_TIMESTAMP(ADDED) AS ADDED FROM phistory WHERE VALUE_ID='".$id."' AND ADDED>=('".date('Y-m-d H:i:s', $time)."') ORDER BY ADDED LIMIT 1");
		$query = new Query('select');
		$sqlHelp = new CoreLib\SqlHelper(Tables\PropertyHistoryTable::getTableName());
		$sql = "SELECT\n\t"
			.$sqlHelp->wrapFieldQuotes('VALUE').", UNIX_TIMESTAMP("
			.$sqlHelp->wrapFieldQuotes('ADDED').") AS "
			.$sqlHelp->wrapQuotes('ADDED')."\nFROM\n\t"
			.$sqlHelp->wrapTableQuotes()."\nWHERE\n\t"
			.$sqlHelp->wrapFieldQuotes('VALUE_ID')."='".intval($id)."' AND\n\t"
			.$sqlHelp->wrapFieldQuotes('ADDED').">=('".date('Y-m-d H:i:s', $time)."')\n"
			."ORDER BY "
			.$sqlHelp->wrapFieldQuotes('ADDED')." DESC\nLIMIT 1";
		$query->setQueryBuildParts($sql);
		$val2 = $query->exec()->fetch();


		// Not found values
		if (!isset($val1['VALUE']) && !isset($val2['VALUE']))
		{
			return false;
		}

		// Only before
		if (isset($val1['VALUE']) && !isset($val2['VALUE']))
		{
			return $val1['VALUE'];
		}

		// Only after
		if (!isset($val1['VALUE']) && isset($val2['VALUE']))
		{
			return $val2['VALUE'];
		}

		// Nerest
		if ($nerest)
		{
			if (($time-$val1['ADDED']) < ($val2['ADDED']-$time))
			{
				return $val1['VALUE'];
			}
			else
				return $val2['VALUE'];
		}
		// Interpolation
		else
		{
			if (($val2['ADDED'] - $val1['ADDED']) == 0)
			{
				return $val1['VALUE'];
			}
			else
			{
				return $val1['VALUE'] + ($val2['VALUE'] - $val1['VALUE']) * ($time - $val1['ADDED']) / ($val2['ADDED'] - $val1['ADDED']);
			}
		}
	}

	public static function setGlobal ($varname, $value, $no_linked = 0)
	{
		static::parseVarname($varname,$object_name);

		$obj = self::getObject($object_name);

		if ($obj instanceof Entity\Object)
		{
			$obj->setProperty($varname, $value, $no_linked);
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function callMethod($method_name, $params = 0)
	{
		static::parseMethodname($method_name,$object_name,$varname);

		$obj = static::getObject($object_name);

		if ($obj instanceof Entity\Object)
		{
			return $obj->callMethod($method_name, $params);
		}
		else
		{
			return 0;
		}

	}

	public static function processDaemon ()
	{
		//TODO: Протестировать перед тем, как запускать демона
		//$keep=SQLSelect("SELECT DISTINCT VALUE_ID,KEEP_HISTORY FROM phistory_queue");
		$query = new Query('select');
		$sqlHelp = new CoreLib\SqlHelper(Tables\PropertyHistoryQueueTable::getTableName());
		$sql = "SELECT\n\t"
			."DISTINCT "
			.$sqlHelp->wrapFieldQuotes('VALUE_ID').", "
			.$sqlHelp->wrapFieldQuotes('KEEP_HISTORY')."\nFROM\n\t"
			.$sqlHelp->wrapTableQuotes();
		$query->setQueryBuildParts($sql);
		$keep = array();
		$res = $query->exec();
		while ($ar_res = $res->fetch())
		{
			$keep[] = $ar_res;
		}

		if (isset($keep[0]['VALUE_ID']))
		{
			$total=count($keep);
			for($i=0;$i<$total;$i++)
			{
				$keep_rec=$keep[$i];
				//SQLExec("DELETE FROM phistory WHERE VALUE_ID='".$keep_rec['VALUE_ID']."' AND TO_DAYS(NOW())-TO_DAYS(ADDED)>".(int)$keep_rec['KEEP_HISTORY']);
				$query = new Query('delete');
				$sqlHelp = new CoreLib\SqlHelper(Tables\PropertyHistoryTable::getTableName());
				$sql = "DELETE FROM\n\t"
					.$sqlHelp->wrapTableQuotes()."\nWHERE\n\t"
					.$sqlHelp->wrapFieldQuotes('VALUE_ID')."='".$keep_rec['VALUE_ID']."' AND\n\t"
					."TO_DAYS(NOW())-TO_DAYS("
					.$sqlHelp->wrapFieldQuotes('ADDED').")>".intval($keep_rec['KEEP_HISTORY']);
				$query->setQueryBuildParts($sql);
				$query->exec();
			}
		}

		//$queue=SQLSelect("SELECT * FROM phistory_queue ORDER BY ID LIMIT 500");
		$queue = Tables\PropertyHistoryQueueTable::getList(
			array(
				'order' => array('ID'=>'ASC'),
				'limit' => 500
			)
		);

		if (isset($queue[0]['ID']))
		{
			$total=count($queue);
			for($i=0;$i<$total;$i++)
			{
				$q_rec=$queue[$i];
				$value=$q_rec['VALUE'];
				$old_value=$q_rec['OLD_VALUE'];

				//SQLExec("DELETE FROM phistory_queue WHERE ID='".$q_rec['ID']."'");
				Tables\PropertyHistoryQueueTable::delete($q_rec['ID']);

				if ($value!=$old_value || (defined('HISTORY_NO_OPTIMIZE') && HISTORY_NO_OPTIMIZE==1))
				{
					//SQLExec("DELETE FROM phistory WHERE VALUE_ID='".$q_rec['VALUE_ID']."' AND TO_DAYS(NOW())-TO_DAYS(ADDED)>".(int)$q_rec['KEEP_HISTORY']);
					$query = new Query('delete');
					$sqlHelp = new CoreLib\SqlHelper(Tables\PropertyHistoryTable::getTableName());
					$sql = "DELETE FROM\n\t"
						.$sqlHelp->wrapTableQuotes()."\nWHERE\n\t"
						.$sqlHelp->wrapFieldQuotes('VALUE_ID')."='".$q_rec['VALUE_ID']."' AND\n\tTO_DAYS(NOW())-TO_DAYS("
						.$sqlHelp->wrapFieldQuotes('ADDED').")>".intval($q_rec['KEEP_HISTORY']);
					$query->setQueryBuildParts($sql);
					$query->exec();

					$h=array();
					$h['VALUE_ID']=$q_rec['VALUE_ID'];
					$h['ADDED']=$q_rec['ADDED'];
					$h['VALUE']=$value;
					//$h['ID']=SQLInsert('phistory', $h);
					$h['ID'] = Tables\PropertyHistoryTable::add(array("VALUES"=>$h))->getInsertId();
				}
				elseif ($value==$old_value)
				{
					//$tmp_history=SQLSelect("SELECT * FROM phistory WHERE VALUE_ID='".$q_rec['VALUE_ID']."' ORDER BY ID DESC LIMIT 2");
					$tmp_history = Tables\PropertyHistoryTable::getList(
						array(
							'filter' => array(
								'VALUE_ID' => $q_rec['VALUE_ID']
							),
							'order' => array('ID'=>'DESC'),
							'limit' => 2
						)
					);
					$prev_value=$tmp_history[0]['VALUE'];
					$prev_prev_value=$tmp_history[1]['VALUE'];
					if ($prev_value==$prev_prev_value)
					{
						$tmp_history[0]['ADDED']=$q_rec['ADDED'];
						$tmpID = $tmp_history[0]['ID'];
						unset($tmp_history[0]['ID']);
						//SQLUpdate('phistory', $tmp_history[0]);
						Tables\PropertyHistoryTable::update($tmpID,array("VALUES"=>$tmp_history[0]));
						$tmp_history[0]['ID'] = $tmpID;
						unset($tmpID);

					}
					else
					{
						$h=array();
						$h['VALUE_ID']=$q_rec['VALUE_ID'];
						$h['ADDED']=$q_rec['ADDED'];
						$h['VALUE']=$value;
						//$h['ID']=SQLInsert('phistory', $h);
						$h['ID'] = Tables\PropertyHistoryTable::add(array("VALUES"=>$h))->getInsertId();
					}
				}

			}
		}
	}

	/*
	 * Short Aliases
	 */
	public static function sg($varname, $value, $no_linked = 0)
	{
		return static::setGlobal($varname, $value, $no_linked);
	}

	public static function gg($varname)
	{
		return static::getGlobal($varname);
	}

	public static function cm($method_name, $params = 0)
	{
		return static::callMethod($method_name, $params);
	}

	/**
	 * @deprecated
	 * @see Objects::callMethod
	 *
	 * @param     $method_name
	 * @param int $params
	 *
	 * @return bool|int
	 */
	public static function runMethod($method_name, $params = 0)
	{
		return static::callMethod($method_name, $params);
	}



	/** Интерфейсы административной панели*/

	/**
	 * @param int $parentID
	 *
	 * @return string
	 */
	public static function getTreeView ($parentID=0)
	{
		global $USER;
		$adminDir = CoreLib\Loader::getSitePublic('kuzmahome').'admin/';
		$html = '';

		$arClasses = Tables\ClassesTable::getList(
			array(
				'select' => array('ID','TITLE','DESCRIPTION'),
				'filter' => array('PARENT_ID'=>intval($parentID)),
				'order' => array('TITLE'=>'ASC')
			)
		);
		if ($arClasses)
		{
			$html.='<table class="table"><tbody>';

			foreach ($arClasses as $arClass)
			{
				$bNoObjects = false;
				$arObjects = Tables\ObjectsTable::getList(
					array(
						'select' => array('ID','TITLE','DESCRIPTION'),
						'filter' => array('CLASS_ID'=>intval($arClass['ID'])),
						'order' => array('TITLE'=>'ASC')
					)
				);
				if (!$arObjects)
				{
					$bNoObjects = true;
				}
				//Начало описания класса
				$html.='<tr';
				/*
				if (intval($parentID)>0)
				{
					$html.=' class="sublist-'.intval($parentID).'"';
					if ($USER->getUserCookie('classes-view-'.$parentID)===1)
					{
						$html.=' style="display: block;"';
					}
					else
					{
						$html.=' style="display: none;"';
					}
				}
				*/
				$html.='><td valign="top">';

				$html.='<a href="#" id="link-'.$arClass['ID'].'"';
				if (
					$USER->issetUserCookie('classes-view-'.$arClass['ID'])===true
					&& intval($USER->getUserCookie('classes-view-'.$arClass['ID']))==1
				)
				{
					$html.=' data-comm="hide"';
				}
				else
				{
					$html.=' data-comm="show"';
				}
				$html.=' onclick="return showHideClasses('.$arClass['ID'].');"';
				$html.=' class="show-hide-link btn btn-default btn-sm expand">';
				if (
					$USER->issetUserCookie('classes-view-'.$arClass['ID'])===true
					&& intval($USER->getUserCookie('classes-view-'.$arClass['ID']))==1
				)
				{
					$html.='-';
				}
				else
				{
					$html.='+';
				}
				$html.='</a><b>'.$arClass['TITLE'].'</b>';

				if (strlen($arClass['DESCRIPTION'])>0)
				{
					$html.='<i>&nbsp;&nbsp;'.$arClass['DESCRIPTION'].'</i>';
				}

				$html.='</td><td valign="top" align="right">';

				//Кнопки редактирования класса
				$html.='<a href="'.$adminDir.'objects/class_edit.php?id='.$arClass['ID']
					.'" class="btn btn-default btn-sm" title="Редактировать"><i class="glyphicon glyphicon-pencil"></i></a>'
					.'<a href="'.$adminDir.'objects/class_properties_list.php?id='.$arClass['ID']
					.'" class="btn btn-default btn-sm" title="Свойства"><i class="glyphicon glyphicon-th"></i></a>'
					.'<a href="'.$adminDir.'objects/class_methods_list.php?id='.$arClass['ID']
					.'" class="btn btn-default btn-sm" title="Методы"><i class="glyphicon glyphicon-th-list"></i></a>'
					.'<a href="'.$adminDir.'objects/class_objects_list.php?id='.$arClass['ID']
					.'" class="btn btn-default btn-sm" title="Объекты"><i class="glyphicon glyphicon-th-large"></i></a>'
					.'<a href="'.$adminDir.'objects/class_add_child.php?id='.$arClass['ID'].'" class="btn btn-default btn-sm" title="Расширить"><i class=""></i>Расширить</a>';
				if($bNoObjects)
				{
					$html.='<a href="'.$adminDir.'objects/index.php?deleteClass='.$arClass['ID'] //.'&id='.$arClass['ID']
						.'" class="btn btn-default btn-sm" title="Удалить" onclick="'."return confirm('Вы действительно хотите удалить класс ".$arClass['TITLE']."?')"
						.'"><i class="glyphicon glyphicon-remove"></i></a>';
				}
				//---end Кнопки редактирования

				$html.='</td></tr>';

				//Объекты класса и подклассы
				if (!$bNoObjects)
				{
					$html.='<tr class="sublist-'.$arClass['ID'];
					if (
						$USER->issetUserCookie('classes-view-'.$arClass['ID'])===true
						&& intval($USER->getUserCookie('classes-view-'.$arClass['ID']))==1
					)
					{
						$html.=' show';
					}
					else
					{
						$html.=' hide';
					}
					$html.='">';

					//Объекты класса
					$html.='<td valign="top" colspan="2"><div><table border="0"><tbody>';

					foreach ($arObjects as $arObject)
					{
						$html.='<tr><td><a href="'.$adminDir.'objects/class_object_edit.php?classID='.$arClass['ID'].'&id='.$arObject['ID'].'">'.$arObject['TITLE'].'</a>';
						$html.='</td><td>&nbsp;';
						if (strlen($arObject['DESCRIPTION'])>0)
						{
							$html.=$arObject['DESCRIPTION'];
						}
						$html.='</td></tr>';


						$arMethods = Tables\MethodsTable::getList(
							array(
								'select' => array('ID','TITLE','DESCRIPTION'),
								'filter' => array('OBJECT_ID'=>intval($arObject['ID'])),
								'order' => array('TITLE'=>'ASC')
							)
						);
						if ($arMethods)
						{
							//Переопределенные методы объекта класса
							$html.='<tr><td>&nbsp;</td><td><small><ul>';

							foreach ($arMethods as $arMethod)
							{
								$html.='<li><a href="'.$adminDir.'objects/object_method_edit.php?classID='.$arClass['ID'].'&objectID='.$arObject['ID'].'&id='.$arMethod['ID'].'">'.$arMethod['TITLE'].'</a>';
								if (strlen($arMethod['DESCRIPTION'])>0)
								{
									$html.=' - '.$arMethod['DESCRIPTION'];
								}
								$html.='</li>';
							}

							$html.='</ul></small></td></tr>';
							//---end Переопределенные методы...
						}
					}

					$html.='</tbody></table></div></td>';
					//---end Объекты класса...

					$html.='</tr>';
				}
				//---end Объекты классов и...

				//Если есть подклассы
				$arChild = Tables\ClassesTable::getList(
					array(
						'select' => array('ID'),
						'filter' => array('PARENT_ID'=>intval($arClass['ID'])),
						'order' => array('TITLE'=>'ASC'),
						'limit' => 1
					)
				);
				if ($arChild && isset($arChild[0]))
				{
					$arChild = $arChild[0];
				}
				if ($arChild)
				{
					$html.='<tr class="sublist-'.$arClass['ID'];
					if (
						$USER->issetUserCookie('classes-view-'.$arClass['ID'])===true
						&& intval($USER->getUserCookie('classes-view-'.$arClass['ID']))==1
					)
					{
						$html.=' show';
					}
					else
					{
						$html.=' hide';
					}
					$html.='">';

					$html.='<td style="padding-left:40px" colspan="2">';

					$html.=static::getTreeView($arClass['ID']);

					$html.='</td></tr>';
				}

			}

			$html.='</tbody></table>';

		}


		return $html;
	}

	public static function showCodemirrorScript ($codeField='code')
	{
		?>
		<script language="javascript">
			var myTextAreacode;
			$(document).ready(function(){
				var myTextAreacode=document.getElementById('<?=$codeField?>');
				var editor = CodeMirror.fromTextArea(myTextAreacode, {
					value: myTextAreacode.value,
					lineNumbers: true,
					matchBrackets: true,

					mode: "text/x-php",

					indentUnit: 3,
					tabSize: 3,
					firstLineNumber: 1,
					indentWithTabs: false,
					autoCloseBrackets: true,
					foldGutter: true,
					gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
					extraKeys: {
						"F11": function(cm) {
							cm.setOption("fullScreen", !cm.getOption("fullScreen"));
						},
						"Ctrl-S": function(instance) { document.getElementById('code').form.submit(); },
						"Esc": function(cm) {
							if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
						},
						"Ctrl-Space": "autocomplete",
						"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }
					}
				});
			});
		</script>
		<?
	}


	private static function addShift ($lvl=0)
	{
		$shift = '';
		for ($i=0; $i<=$lvl; $i++)
		{
			$shift.="\t";
		}

		return $shift;
	}

	private static function getHistoryFunc ($varname, $start_time = 0, $stop_time = 0, $func='MIN')
	{
		if ($start_time <= 0)
		{
			$start_time = (time() + $start_time);
		}
		if ($stop_time  <= 0)
		{
			$stop_time  = (time() + $stop_time);
		}

		// Get hist val id
		$id = static::getHistoryValueId($varname);

		// Get data
		//$data = SQLSelectOne("SELECT MIN(VALUE+0.0) AS VALUE FROM phistory "."WHERE VALUE != \"\" AND VALUE_ID='".$id."' AND ADDED>=('".date('Y-m-d H:i:s', $start_time)."') AND ADDED<=('".date('Y-m-d H:i:s', $stop_time)."')");
		$query = new Query('select');
		$sqlHelp = new CoreLib\SqlHelper(Tables\PropertyHistoryTable::getTableName());
		$sql = "SELECT\n\t".$func."("
			.$sqlHelp->wrapFieldQuotes('VALUE')."+0.0) AS "
			.$sqlHelp->wrapQuotes('VALUE')."\nFROM\n\t"
			.$sqlHelp->wrapTableQuotes()."\nWHERE\n\t"
			.$sqlHelp->wrapFieldQuotes('VALUE')." != \"\" AND\n\t"
			.$sqlHelp->wrapFieldQuotes('VALUE_ID')."='".intval($id)."' AND\n\t"
			.$sqlHelp->wrapFieldQuotes('ADDED').">=('".date('Y-m-d H:i:s', $start_time)."') AND\n\t"
			.$sqlHelp->wrapFieldQuotes('ADDED')."<=('".date('Y-m-d H:i:s', $stop_time)."')\nLIMIT 1";
		$query->setQueryBuildParts($sql);
		$data = $query->exec()->fetch();

		if (!isset($data['VALUE']))
		{
			return false;
		}

		return $data['VALUE'];
	}

	private static function parseVarname (&$varname, &$object_name)
	{
		$tmp = explode('.', $varname);

		if (isset($tmp[2]))
		{
			$object_name = $tmp[0] . '.' . $tmp[1];
			$varname     = $tmp[2];
		}
		elseif (isset($tmp[1]))
		{
			$object_name = $tmp[0];
			$varname     = $tmp[1];
		}
		else
		{
			$object_name = static::$systemObject;
		}
	}

	private static function parseMethodname (&$method_name, &$object_name, &$varname)
	{
		$varname = null;
		$tmp = explode('.', $method_name);

		if (isset($tmp[2]))
		{
			$object_name = $tmp[0] . '.' . $tmp[1];
			$varname     = $tmp[2];
		}
		elseif (isset($tmp[1]))
		{
			$object_name = $tmp[0];
			$method_name = $tmp[1];
		}
		else
		{
			$object_name = static::$systemObject;
		}
	}


}