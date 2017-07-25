<?php

namespace MSergeev\Packages\Kuzmahome\Entity;

use MSergeev\Core\Entity\Query;
use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Lib;
use MSergeev\Packages\Kuzmahome\Tables;

class Object
{
	public $id = null;
	public $title = null;
	public $description = null;
	public $classID = null;
	public $roomID = null;
	public $keepHistory = null;
	private $system = null;
	private $arProperties = array();
	private $property_linked_history = array();

	public function __construct ()
	{

	}

	public function setID ($id)
	{
		$this->id = $id;
	}

	public function clone_object($id)
	{
		//$rec=SQLSelectOne("SELECT * FROM objects WHERE ID='".$id."'");
		$rec = Tables\ObjectsTable::getList(
			array(
				'filter' => array('ID'=>intval($id)),
				'limit' => 1
			)
		);
		if ($rec && isset($rec[0]))
		{
			$rec = $rec[0];
		}
		if (!$rec)
		{
			return false;
		}
		$rec['TITLE']=$rec['TITLE'].' (copy)';
		//$rec['ID']=SQLInsert('objects', $rec);
		$rec['ID'] = Tables\ObjectsTable::add(array("VALUES"=>$rec))->getInsertId();

		$seen_pvalues=array();
		//$properties=SQLSelect("SELECT * FROM properties WHERE OBJECT_ID='".$id."'");
		$properties = Tables\PropertiesTable::getList(
			array(
				'filter' => array('OBJECT_ID'=>intval($id))
			)
		);
		$total=count($properties);
		for($i=0;$i<$total;$i++)
		{
			$p_id=$properties[$i]['ID'];
			unset($properties[$i]['ID']);
			$properties[$i]['OBJECT_ID']=$rec['ID'];
			//$properties[$i]['ID']=SQLInsert('properties', $properties[$i]);
			$properties[$i]['ID'] = Tables\PropertiesTable::add(array("VALUES"=>$properties[$i]))->getInsertId();
			//$p_value=SQLSelectOne("SELECT * FROM pvalues WHERE PROPERTY_ID='".$p_id."'");
			$p_value = Tables\PropertyValuesTable::getList(
				array(
					'filter' => array('PROPERTY_ID'=>intval($p_id)),
					'limit' => 1
				)
			);
			if ($p_value && isset($p_value[0]))
			{
				$p_value = $p_value[0];
			}
			if ($p_value['ID'])
			{
				$seen_pvalues[$p_value['ID']]=1;
				unset($p_value['ID']);
				$p_value['PROPERTY_ID']=$properties[$i]['ID'];
				$p_value['OBJECT_ID']=$rec['ID'];
				//SQLInsert('pvalues', $p_value);
				Tables\PropertyValuesTable::add(array("VALUES"=>$p_value));
			}
		}

		//$pvalues=SQLSelect("SELECT * FROM pvalues WHERE OBJECT_ID='".$id."'");
		$pvalues = Tables\PropertyValuesTable::getList(
			array(
				'filter' => array('OBJECT_ID'=>intval($id))
			)
		);
		$total=count($properties);
		for($i=0;$i<$total;$i++)
		{
			$p_id=$pvalues[$i]['ID'];
			if ($seen_pvalues[$p_id])
			{
				continue;
			}
			unset($pvalues[$i]['ID']);
			$pvalues[$i]['OBJECT_ID']=$rec['ID'];
			//$pvalues[$i]['ID']=SQLInsert('pvalues', $pvalues[$i]);
			$pvalues[$i]['ID'] = Tables\PropertyValuesTable::add(array("VALUES"=>$pvalues[$i]))->getInsertId();
		}

		//$methods=SQLSelect("SELECT * FROM methods WHERE OBJECT_ID='".$id."'");
		$methods = Tables\MethodsTable::getList(
			array(
				'filter' => array('OBJECT_ID'=>intval($id))
			)
		);
		$total=count($methods);
		for($i=0;$i<$total;$i++)
		{
			unset($methods[$i]['ID']);
			$methods[$i]['OBJECT_ID']=$rec['ID'];
			//$methods[$i]['ID']=SQLInsert('methods', $methods[$i]);
			$methods[$i]['ID'] = Tables\MethodsTable::add(array("VALUES"=>$methods[$i]))->getInsertId();
		}

		//$this->redirect("?view_mode=edit_objects&id=".$rec['ID']);
		return $rec['ID'];
	}

	public function delete_objects($id)
	{
		//$rec=SQLSelectOne("SELECT * FROM objects WHERE ID='$id'");
		$rec = Tables\ObjectsTable::getList(
			array(
				'filter' => array('ID'=>intval($id)),
				'limit' => 1
			)
		);
		if ($rec && isset($rec[0]))
		{
			$rec = $rec[0];
		}
		if (!$rec)
		{
			return;
		}
		// some action for related tables
		/*
		SQLExec("DELETE FROM history WHERE OBJECT_ID='".$rec['ID']."'");
		SQLExec("DELETE FROM methods WHERE OBJECT_ID='".$rec['ID']."'");
		SQLExec("DELETE FROM pvalues WHERE OBJECT_ID='".$rec['ID']."'");
		SQLExec("DELETE FROM properties WHERE OBJECT_ID='".$rec['ID']."'");
		SQLExec("DELETE FROM objects WHERE ID='".$rec['ID']."'");
		*/
		Tables\ObjectsTable::delete($rec['ID'],true);
	}

	public function loadObject ($id=null)
	{
		if (!is_null($id))
		{
			$this->setID($id);
		}

		$arRes = Tables\ObjectsTable::getList(
			array(
				'filter' => array(
					'ID' => $this->id
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
			$this->title        = $arRes['TITLE'];
			$this->description  = $arRes['DESCRIPTION'];
			$this->classID      = $arRes['CLASS_ID'];
			$this->roomID       = $arRes['ROOM_ID'];
			$this->keepHistory  = $arRes['KEEP_HISTORY'];
			$this->system       = $arRes['SYSTEM'];
		}
	}

	public function getProperty ($property)
	{
		if (isset($this->arProperties[$property]))
		{
			return $this->arProperties[$property];
		}
		else
		{
			$arRes = Tables\PropertyValuesTable::getList(
				array(
					'select' => array('VALUE'),
					'filter' => array(
						'PROPERTY_NAME' => $this->title.'.'.$property
					),
					'limit' => 1
				)
			);
			if ($arRes && isset($arRes[0]))
			{
				$arRes = $arRes[0];
			}

			if (isset($arRes['VALUE']))
			{
				return $arRes['VALUE'];
			}
			else
			{
				return false;
			}
		}
	}

	public function getPropertyByName ($name, $class_id=null, $object_id=null)
	{
		if (is_null($class_id))
		{
			$class_id = $this->classID;
		}
		if (is_null($object_id))
		{
			$object_id = $this->id;
		}
		//$rec=SQLSelectOne("SELECT ID FROM properties WHERE OBJECT_ID='".(int)$object_id."' AND TITLE LIKE '".DBSafe($name)."'");
		$rec = Tables\PropertiesTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array(
					'OBJECT_ID' => intval($object_id),
					'TITLE' => $name
				),
				'limit' => 1
			)
		);
		if ($rec && isset($rec[0]))
		{
			$rec = $rec[0];
		}
		if ($rec['ID']) {
			return $rec['ID'];
		}

		//include_once(DIR_MODULES.'classes/classes.class.php');
		//$cl=new classes();
		//$props=$cl->getParentProperties($class_id, '', 1);
		$props=$this->getParentProperties($class_id, '', 1);

		$total=count($props);
		for($i=0;$i<$total;$i++) {
			if (strtolower($props[$i]['TITLE'])==strtolower($name)) {
				return $props[$i]['ID'];
			}
		}

		return false;

	}

	public function getParentProperties($id, $def='', $include_self=0)
	{
		//$class=SQLSelectOne("SELECT * FROM classes WHERE ID='".(int)$id."'");
		$class = Tables\ClassesTable::getList(
			array(
				'filter' => array('ID'=>intval($id)),
				'limit' => 1
			)
		);
		if ($class && isset($class[0]))
		{
			$class = $class[0];
		}

		//$properties=SQLSelect("SELECT properties.*, classes.TITLE as CLASS_TITLE FROM properties LEFT JOIN classes ON properties.CLASS_ID=classes.ID WHERE CLASS_ID='".$id."' AND OBJECT_ID=0");
		$query = new Query('select');
		$sqlHelpProp = new CoreLib\SqlHelper(Tables\PropertiesTable::getTableName());
		$sqlHelpClass = new CoreLib\SqlHelper(Tables\ClassesTable::getTableName());
		$sql = "SELECT\n\t"
			.$sqlHelpProp->wrapTableQuotes().".*, "
			.$sqlHelpClass->wrapFieldQuotes('TITLE')." as "
			.$sqlHelpClass->wrapQuotes('CLASS_TITLE')."\nFROM\n\t"
			.$sqlHelpProp->wrapTableQuotes()."\n"
			."LEFT JOIN ".$sqlHelpClass->wrapTableQuotes()." ON "
			.$sqlHelpProp->wrapFieldQuotes('CLASS_ID')."="
			.$sqlHelpClass->wrapFieldQuotes('ID')."\nWHERE\n\t"
			.$sqlHelpProp->wrapFieldQuotes('CLASS_ID')."=".intval($id)." AND\n\t"
			.$sqlHelpProp->wrapFieldQuotes('OBJECT_ID')." IS NULL";
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		$properties = array();
		while ($ar_res = $res->fetch())
		{
			$properties[] = $ar_res;
		}

		if ($include_self)
		{
			$res=$properties;
		}
		else
		{
			$res=array();
		}

		if (!is_array($def))
		{
			$def=array();
			foreach($properties as $p)
			{
				$def[]=$p['TITLE'];
			}
		}

		foreach($properties as $p)
		{
			if (!in_array($p['TITLE'], $def))
			{
				$res[]=$p;
				$def[]=$p['TITLE'];
			}
		}

		if ($class['PARENT_ID'])
		{
			$p_res=$this->getParentProperties($class['PARENT_ID'], $def);
			if ($p_res[0]['ID'])
			{
				$res=array_merge($res, $p_res);
			}
		}

		return $res;

	}

	public function getParentMethods($id, $def='', $include_self=0)
	{
		//$class=SQLSelectOne("SELECT * FROM classes WHERE ID='".(int)$id."'");
		$class = Tables\ClassesTable::getList(
			array(
				'filter' => array('ID'=>intval($id)),
				'limit' => 1
			)
		);
		if ($class && isset($class[0]))
		{
			$class = $class[0];
		}

		//$methods=SQLSelect("SELECT methods.*, classes.TITLE as CLASS_TITLE FROM methods LEFT JOIN classes ON methods.CLASS_ID=classes.ID WHERE CLASS_ID='".$id."' AND OBJECT_ID=0");
		$query = new Query('select');
		$sqlHelpMethod = new CoreLib\SqlHelper(Tables\MethodsTable::getTableName());
		$sqlHelpClass = new CoreLib\SqlHelper(Tables\ClassesTable::getTableName());
		$sql = "SELECT\n\t"
			.$sqlHelpMethod->wrapTableQuotes().".*, "
			.$sqlHelpClass->wrapFieldQuotes('TITLE')." as "
			.$sqlHelpClass->wrapQuotes('CLASS_TITLE')."\nFROM\n\t"
			.$sqlHelpMethod->wrapTableQuotes()."\n"
			."LEFT JOIN "
			.$sqlHelpClass->wrapTableQuotes()." ON "
			.$sqlHelpMethod->wrapFieldQuotes('CLASS_ID')."="
			.$sqlHelpClass->wrapFieldQuotes('ID')."\nWHERE\n\t"
			.$sqlHelpMethod->wrapFieldQuotes('CLASS_ID')."=".intval($id)." AND\n\t"
			.$sqlHelpMethod->wrapFieldQuotes('OBJECT_ID')." IS NULL";
		$query->setQueryBuildParts($sql);
		$resQ = $query->exec();
		$methods = array();
		while ($ar_res = $resQ->fetch())
		{
			$methods[] = $ar_res;
		}

		if ($include_self)
		{
			$res=$methods;
		}
		else
		{
			$res=array();
		}



		if (!is_array($def))
		{
			$def=array();
			foreach($methods as $p)
			{
				$def[]=$p['TITLE'];
			}
		}

		foreach($methods as $p)
		{
			if (!in_array($p['TITLE'], $def))
			{
				$res[]=$p;
				$def[]=$p['TITLE'];
			}
		}

		if ($class['PARENT_ID'])
		{
			$p_res=$this->getParentMethods($class['PARENT_ID'], $def);
			if ($p_res[0]['ID'])
			{
				$res=array_merge($res, $p_res);
			}
		}

		return $res;

	}

	public function getMethodByName($name, $class_id=null, $id=null)
	{
		if (is_null($class_id))
		{
			$class_id = $this->classID;
		}
		if (is_null($id))
		{
			$id = $this->id;
		}

		if ($id) {
			//$meth=SQLSelectOne("SELECT ID FROM methods WHERE OBJECT_ID='".(int)$id."' AND TITLE LIKE '".DBSafe($name)."'");
			$method = Tables\MethodsTable::getList(
				array(
					'select' => array('ID'),
					'filter' => array(
						'OBJECT_ID' => intval($id),
						'TITLE' => $name
					),
					'limit' => 1
				)
			);
			if ($method && isset($method[0]))
			{
				$method = $method[0];
			}
			if ($method['ID'])
			{
				return $method['ID'];
			}
		}

		//include_once(DIR_MODULES.'classes/classes.class.php');
		//$cl=new classes();
		//$meths=$cl->getParentMethods($class_id, '', 1);
		$meths=$this->getParentMethods($class_id, '', 1);

		$total=count($meths);
		for($i=0;$i<$total;$i++)
		{
			if (strtolower($meths[$i]['TITLE'])==strtolower($name))
			{
				return $meths[$i]['ID'];
			}
		}
		return false;
	}

	/**
	 * @deprecated
	 * @see MSergeev\Packages\Kuzmahome\Entity\Object::callMethod
	 *
	 * @param     $name
	 * @param int $params
	 */
	public function callClassMethod($name, $params=0)
	{
		$this->callMethod($name, $params, 1);
	}

	public function callMethod($name, $params=0, $parent=0)
	{

		//Lib\Perfmonitor::startMeasure('callMethod');

		$original_method_name=$this->title.'.'.$name;

		//Lib\Perfmonitor::startMeasure('callMethod ('.$original_method_name.')');

		if (!$parent) {
			$id=$this->getMethodByName($name/*, $this->classID, $this->id*/);
		} else {
			$id=$this->getMethodByName($name, $this->classID, 0);
		}
		if ($id)
		{
			//$method=SQLSelectOne("SELECT * FROM methods WHERE ID='".$id."'");
			$method = Tables\MethodsTable::getList(
				array(
					'filter' => array('ID'=>intval($id)),
					'limit' => 1
				)
			);
			if ($method && isset($method[0]))
			{
				$method = $method[0];
			}

			$method['EXECUTED']=date('d.m.Y H:i:s');
			if (!$method['OBJECT_ID'])
			{
				if (!$params)
				{
					$params=array();
				}
				$params['ORIGINAL_OBJECT_TITLE']=$this->title;
			}
			if ($params)
			{
				$method['EXECUTED_PARAMS']=serialize($params);
			}
			//SQLUpdate('methods', $method);
			$this->updateTable('MethodsTable',$method);

			if ($method['OBJECT_ID'] && $method['CALL_PARENT']==1)
			{
				$this->callMethod($name, $params, 1);
			}

			$code = '';
			if ($method['SCRIPT_ID'])
			{
				/*
					$script=SQLSelectOne("SELECT * FROM scripts WHERE ID='".$method['SCRIPT_ID']."'");
					$code=$script['CODE'];
				   */
				Lib\Scripts::runScript($method['SCRIPT_ID']);
			}
			else
			{
				$code=$method['CODE'];
			}


			if ($code!='')
			{

				/*
				if (defined('SETTINGS_DEBUG_HISTORY') && SETTINGS_DEBUG_HISTORY==1) {
				 $class_object=SQLSelectOne("SELECT NOLOG FROM classes WHERE ID='".$this->class_id."'");
				 if (!$class_object['NOLOG']) {

				  $prevLog=SQLSelectOne("SELECT ID, UNIX_TIMESTAMP(ADDED) as UNX FROM history WHERE OBJECT_ID='".$this->id."' AND METHOD_ID='".$method['ID']."' ORDER BY ID DESC LIMIT 1");
				  if ($prevLog['ID']) {
				   $prevRun=$prevLog['UNX'];
				   $prevRunPassed=time()-$prevLog['UNX'];
				  }

				  $h=array();
				  $h['ADDED']=date('Y-m-d H:i:s');
				  $h['OBJECT_ID']=$this->id;
				  $h['METHOD_ID']=$method['ID'];
				  $h['DETAILS']=serialize($params);
				  if ($parent) {
				   $h['DETAILS']='(parent method) '.$h['DETAILS'];
				  }
				  $h['DETAILS'].="\n".'code: '."\n".$code;
				  SQLInsert('history', $h);
				 }
				}
				*/


				try
				{
					$success = eval($code);
					if ($success === false)
					{
						//getLogger($this)->error(sprintf('Error in "%s.%s" method.', $this->object_title, $name));
						$this->registerError('method', sprintf('Exception in "%s.%s" method.', $this->title, $name));
					}
				}
				catch (\Exception $e)
				{
					//getLogger($this)->error(sprintf('Exception in "%s.%s" method', $this->object_title, $name), $e);
					$this->registerError('method', sprintf('Exception in "%s.%s" method '.$e->getMessage(), $this->title, $name));
				}

			}
			//Lib\Perfmonitor::endMeasure('callMethod', 1);
			//Lib\Perfmonitor::endMeasure('callMethod ('.$original_method_name.')', 1);
			if ($method['OBJECT_ID'] && $method['CALL_PARENT']==2)
			{
				$parent_success=$this->callMethod($name, $params, 1);
			}
			else
			{
				$parent_success=true;
			}

			if (isset($success))
			{
				return $success;
			}
			else
			{
				return $parent_success;
			}

		}
		else
		{
			//Lib\Perfmonitor::endMeasure('callMethod ('.$original_method_name.')', 1);
			//Lib\Perfmonitor::endMeasure('callMethod', 1);
			return false;
		}
	}

	public function setProperty($property, $value, $no_linked=0)
	{

		//Lib\Perfmonitor::startMeasure('setProperty');
		//Lib\Perfmonitor::startMeasure('setProperty ('.$property.')');

		if (is_null($value))
		{
			$value='';
		}

		$id=$this->getPropertyByName($property /*, $this->classID, $this->id*/);
		$old_value='';

		//$cached_name='MJD:'.$this->title.'.'.$property;

		//Lib\Perfmonitor::startMeasure('setproperty_update');
		if ($id)
		{
			//$prop=SQLSelectOne("SELECT * FROM properties WHERE ID='".$id."'");
			$prop = Tables\PropertiesTable::getList(
				array(
					'filter' => array('ID'=>intval($id)),
					'limit' => 1
				)
			);
			if ($prop && isset($prop[0]))
			{
				$prop = $prop[0];
			}
			//$v=SQLSelectOne("SELECT * FROM pvalues WHERE PROPERTY_ID='".(int)$id."' AND OBJECT_ID='".(int)$this->id."'");
			$v = Tables\PropertyValuesTable::getList(
				array(
					'filter' => array(
						'PROPERTY_ID' => intval($id),
						'OBJECT_ID' => intval($this->id)
					),
					'limit' => 1
				)
			);
			if ($v && isset($v[0]))
			{
				$v = $v[0];
			}
			$old_value=$v['VALUE'];
			$v['VALUE']=$value;
			if ($v['ID'])
			{
				$v['UPDATED']=date('d.m.Y H:i:s');
				//if ($old_value!=$value) {
				//SQLUpdate('pvalues', $v);
				$this->updateTable('PropertyValuesTable',$v);
				//} else {
				// SQLExec("UPDATE pvalues SET UPDATED='".$v['UPDATED']."' WHERE ID='".$v['ID']."'");
				//}
			}
			else
			{
				$v['PROPERTY_ID']=$id;
				$v['PROPERTY_NAME'] = $this->title.'.'.$property;
				$v['OBJECT_ID']=$this->id;
				$v['VALUE']=$value;
				$v['UPDATED']=date('d.m.Y H:i:s');
				//$v['ID']=SQLInsert('pvalues', $v);
				$v['ID'] = Tables\PropertyValuesTable::add(array("VALUES"=>$v))->getInsertId();
			}
			//DebMes(" $id to $value ");
		}
		else
		{
			$prop=array();
			$prop['OBJECT_ID']=$this->id;
			$prop['TITLE']=$property;
			//$prop['ID']=SQLInsert('properties', $prop);
			$prop['ID'] = Tables\PropertiesTable::add(array("VALUES"=>$prop))->getInsertId();

			$v['PROPERTY_ID']=$prop['ID'];
			$v['PROPERTY_NAME']=$this->title.'.'.$property;
			$v['OBJECT_ID']=$this->id;
			$v['VALUE']=$value;
			$v['UPDATED']=date('d.m.Y H:i:s');
			//$v['ID']=SQLInsert('pvalues', $v);
			$v['ID'] = Tables\PropertyValuesTable::add(array("VALUES"=>$v))->getInsertId();
		}
		//Lib\Perfmonitor::endMeasure('setproperty_update');

		//saveToCache($cached_name, $value);

		/*
		if (function_exists('postToWebSocket')) {
			startMeasure('setproperty_postwebsocket');
			postToWebSocket($this->object_title.'.'.$property, $value);
			endMeasure('setproperty_postwebsocket');
		}
		*/

		/*
		  if ($this->keep_history>0) {
		   $prop['KEEP_HISTORY']=$this->keep_history;
		  }
		  */

		if (isset($prop['KEEP_HISTORY']) && ($prop['KEEP_HISTORY']>0))
		{
			$q_rec=array();
			$q_rec['VALUE_ID']=$v['ID'];
			$q_rec['ADDED']=date('d.m.Y H:i:s');
			$q_rec['VALUE']=$value;
			$q_rec['OLD_VALUE']=$old_value;
			$q_rec['KEEP_HISTORY']=$prop['KEEP_HISTORY'];
			//SQLInsert('phistory_queue', $q_rec);
			Tables\PropertyHistoryQueueTable::add(array("VALUES"=>$q_rec));
		}


		if (isset($prop['ONCHANGE']) && $prop['ONCHANGE'])
		{
			$property_linked_history = &$this->property_linked_history;
			if (!$property_linked_history[$property][$prop['ONCHANGE']])
			{
				$property_linked_history[$property][$prop['ONCHANGE']]=1;
				$params=array();
				$params['PROPERTY']=$property;
				$params['NEW_VALUE']=strval($value);
				$params['OLD_VALUE']=strval($old_value);
				$this->callMethod($prop['ONCHANGE'], $params);
				unset($property_linked_history[$property][$prop['ONCHANGE']]);
			}
		}

		/*
		if (isset($v['LINKED_MODULES']) && $v['LINKED_MODULES'])
		{ // TO-DO !
			if (!is_array($no_linked) && $no_linked)
			{
				return;
			}
			elseif (!is_array($no_linked))
			{
				$no_linked=array();
			}


			$tmp=explode(',', $v['LINKED_MODULES']);
			$total=count($tmp);


			//Lib\Perfmonitor::startMeasure('linkedModulesProcessing');
			for($i=0;$i<$total;$i++)
			{
				$linked_module=trim($tmp[$i]);

				if (isset($no_linked[$linked_module]))
				{
					continue;
				}

				//Lib\Perfmonitor::startMeasure('linkedModule'.$linked_module);
				if (file_exists(DIR_MODULES.$linked_module.'/'.$linked_module.'.class.php')) {
					include_once(DIR_MODULES.$linked_module.'/'.$linked_module.'.class.php');
					$module_object=new $linked_module;
					if (method_exists($module_object, 'propertySetHandle')) {
						$module_object->propertySetHandle($this->title, $property, $value);
					}
				}
				//Lib\Perfmonitor::endMeasure('linkedModule'.$linked_module);
			}
			//Lib\Perfmonitor::endMeasure('linkedModulesProcessing');
		}
		*/
		/**Вместо существующей привязки к модулю поставил обработчик событий изменения свойства объекта*/
		/* Заменил громоздкий вызов на более короткий
		if ($arEvents = CoreLib\Events::getPackageEvents('kuzmahome','OnAfterSetProperty'))
		{
			foreach ($arEvents as $sort=>$ar_events)
			{
				foreach ($ar_events as $arEvent)
				{
					CoreLib\Events::executePackageEvent($arEvent,array($this->title,$property,$value));
				}
			}
		}
		*/
		//Событие изменения любого свойства
		CoreLib\Events::runEvents('kuzmahome','OnAfterSetProperty',array($this->title,$property,$value));
		//Событие изменения именно этого свойства этого объекта (например OnAfterSetProperty:user_msergeev.atHome)
		CoreLib\Events::runEvents('kuzmahome','OnAfterSetProperty:'.$this->title.'.'.$property,array($this->title,$property,$value));


		/*
		   $h=array();
		   $h['ADDED']=date('Y-m-d H:i:s');
		   $h['OBJECT_ID']=$this->id;
		   $h['VALUE_ID']=$v['ID'];
		   $h['OLD_VALUE']=$old_value;
		   $h['NEW_VALUE']=$value;
		   SQLInsert('history', $h);
		  */


		//Lib\Perfmonitor::endMeasure('setProperty ('.$property.')', 1);
		//Lib\Perfmonitor::endMeasure('setProperty', 1);

	}

	private function updateTable ($class, $arData)
	{
		$dataID = $arData['ID'];
		unset($arData['ID']);
		$class = 'MSergeev\Packages\Kuzmahome\Tables'."\\".$class;
		$class::update($dataID,array("VALUES"=>$arData));
	}

	private function registerError ($name, $text)
	{
		Lib\Logs::debMes('Error '.$name.': '.$text);
	}
}