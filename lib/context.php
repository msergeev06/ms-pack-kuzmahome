<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;

class Context
{
	/**
	 * @deprecated
	 * @see $USER->getID()
	 *
	 * @return int
	 */
	public static function context_getuser()
	{
		global $USER;

		return $USER->getID();
	}

	/**
	 * @deprecated
	 * @see MSergeev\Packages\Kuzmahome\Lib\Context::contextGetCurrent
	 *
	 * @return int|string
	 */
	public static function context_getcurrent()
	{
		return self::contextGetCurrent();
	}

	/**
	 * @deprecated
	 * @see MSergeev\Packages\Kuzmahome\Lib\Context::contextGetHistory
	 *
	 * @return string
	 */
	public static function context_get_history()
	{
		return self::contextGetHistory();
	}

	/**
	 * @deprecated
	 * @see MSergeev\Packages\Kuzmahome\Lib\Context::contextClear
	 */
	public static function context_clear()
	{
		self::contextClear();
	}

	/**
	 * @deprecated
	 * @see MSergeev\Packages\Kuzmahome\Lib\Context::contextActivate
	 *
	 * @param        $id
	 * @param int    $no_action
	 * @param string $history
	 */
	public static function context_activate($id, $no_action = 0, $history = '')
	{
		self::contextActivate($id, $no_action, $history);
	}

	/**
	 * @deprecated
	 * @see MSergeev\Packages\Kuzmahome\Lib\Context::contextTimeout
	 *
	 * @param $context
	 * @param $user
	 */
	public static function context_timeout ($context, $user)
	{
		self::contextTimeout($context, $user);
	}

	/**
	 * @deprecated
	 * @see MSergeev\Packages\Kuzmahome\Lib\Context::contextActivateExt
	 *
	 * @param        $id
	 * @param int    $timeout
	 * @param string $timeout_code
	 * @param int    $timeout_context_id
	 */
	public static function context_activate_ext($id, $timeout = 0, $timeout_code = '', $timeout_context_id = 0)
	{
		self::contextActivateExt($id, $timeout, $timeout_code, $timeout_context_id);
	}

	public static function contextGetCurrent()
	{
		global $USER;
		//$user_id = context_getuser();

		//$sqlQuery = "SELECT ID, ACTIVE_CONTEXT_ID, ACTIVE_CONTEXT_EXTERNAL FROM users WHERE ID = '" . $USER->getID() . "'";
		//$user = SQLSelectOne($sqlQuery);
		$user = Tables\UsersTable::getList(
			array(
				'select' => array('ID','ACTIVE_CONTEXT_ID','ACTIVE_CONTEXT_EXTERNAL'),
				'filter' => array('USER_ID'=>$USER->getID()),
				'limit' => 1
			)
		);
		if ($user && isset($user[0]))
		{
			$user = $user[0];
		}

		if (!$user['ID'])
			return 0;

		if ($user['ACTIVE_CONTEXT_EXTERNAL'])
		{
			return 'ext' . (int)$user['ACTIVE_CONTEXT_ID'];
		}
		else
		{
			return (int)$user['ACTIVE_CONTEXT_ID'];
		}
	}

	public static function contextGetHistory()
	{
		global $USER;
		//$user_id = context_getuser();

		//$sqlQuery = "SELECT ID, ACTIVE_CONTEXT_ID, ACTIVE_CONTEXT_EXTERNAL, ACTIVE_CONTEXT_HISTORY FROM users WHERE ID = '" . (int)$user_id . "'";
		//$user = SQLSelectOne($sqlQuery);
		$user = Tables\UsersTable::getList(
			array(
				'select' => array('ID', 'ACTIVE_CONTEXT_ID', 'ACTIVE_CONTEXT_EXTERNAL', 'ACTIVE_CONTEXT_HISTORY'),
				'filter' => array('USER_ID'=>$USER->getID()),
				'limit' => 1
			)
		);
		if ($user && isset($user[0]))
		{
			$user = $user[0];
		}

		if ($user['ACTIVE_CONTEXT_ID'])
			return $user['ACTIVE_CONTEXT_HISTORY'];

		return '';
	}

	public static function contextClear()
	{
		global $USER;
		//$user_id = context_getuser();

		//$user = SQLSelectOne("SELECT * FROM users WHERE ID = '" . (int)$user_id . "'");
		$user = Tables\UsersTable::getList(
			array(
				'select' => array('ID','ACTIVE_CONTEXT_ID','ACTIVE_CONTEXT_EXTERNAL','ACTIVE_CONTEXT_UPDATED','ACTIVE_CONTEXT_HISTORY'),
				'filter' => array('USER_ID'=>$USER->getID()),
				'limit' => 1
			)
		);
		if ($user && isset($user[0]))
		{
			$user = $user[0];
		}
		if ($user)
		{
			$user['ACTIVE_CONTEXT_ID']       = 0;
			$user['ACTIVE_CONTEXT_EXTERNAL'] = 0;
			$user['ACTIVE_CONTEXT_UPDATED']  = date('d.m.Y H:i:s');
			$user['ACTIVE_CONTEXT_HISTORY']  = '';
			$userID = $user['ID'];
			unset($user['ID']);
			//SQLUpdate('users', $user);
			Tables\UsersTable::update($userID,array("VALUES"=>$user));
		}
	}

	public static function contextActivate($id, $no_action = 0, $history = '', $user_id=null)
	{
		global $USER;
		if (is_null($user_id))
		{
			$user_id = $USER->getID();
		}

		//$user_id = context_getuser();
		//$user    = SQLSelectOne("SELECT * FROM users WHERE ID = '" . (int)$user_id . "'");
		$user = Tables\UsersTable::getList(
			array(
				'select' => array('ID','ACTIVE_CONTEXT_ID','ACTIVE_CONTEXT_EXTERNAL','ACTIVE_CONTEXT_UPDATED','ACTIVE_CONTEXT_HISTORY'),
				'filter' => array('USER_ID'=>$user_id),
				'limit' => 1
			)
		);
		if ($user && isset($user[0]))
		{
			$user = $user[0];
		}

		$user['ACTIVE_CONTEXT_ID']       = $id;
		$user['ACTIVE_CONTEXT_EXTERNAL'] = 0;
		$user['ACTIVE_CONTEXT_UPDATED']  = date('Y-m-d H:i:s');

		if ($history)
			$user['ACTIVE_CONTEXT_HISTORY'] .= ' ' . $history;

		$userID = $user['ID'];
		unset($user['ID']);
		//SQLUpdate('users', $user);
		Tables\UsersTable::update($userID,array("VALUES"=>$user));

		if ($id)
		{
			//execute pattern
			//$context = SQLSelectOne("SELECT * FROM patterns WHERE ID = '" . (int)$id . "'");
			$context = Tables\PatternsTable::getList(
				array(
					'select' => array('ID','TIMEOUT'),
					'filter' => array('ID'=>intval($id)),
					'limit' => 1
				)
			);
			if ($context && isset($context[0]))
			{
				$context = $context[0];
			}
			$timeout = $context['TIMEOUT'];

			if (!$timeout)
				$timeout = 60;

			$timeoutTitle   = 'user_' . $USER->getID() . '_context_timeout';
			$timeoutCommand = 'context_timeout(' . intval($context['ID']) . ', ' . $USER->getID() . ');';
			Jobs::setTimeOut($timeoutTitle, $timeoutCommand, $timeout);

			if (!$no_action)
			{
				//TODO: Раскомментировать и доделать
				//include_once(DIR_MODULES . 'patterns/patterns.class.php');
				//$pt = new patterns();
				//$pt->runPatternAction((int)$context['ID']);
			}
		}
		else
		{
			self::contextClear();
			Jobs::clearTimeOut('user_' . $USER->getID() . '_context_timeout');
		}
	}

	public static function contextActivateExt($id, $timeout = 0, $timeout_code = '', $timeout_context_id = 0)
	{
		global $USER;
		//$user_id = context_getuser();
		//$user    = SQLSelectOne("SELECT * FROM users WHERE ID = '" . (int)$user_id . "'");
		$user = Tables\UsersTable::getList (
			array (
				'select' => array ('ID', 'ACTIVE_CONTEXT_ID', 'ACTIVE_CONTEXT_EXTERNAL', 'ACTIVE_CONTEXT_UPDATED'),
				'filter' => array ('USER_ID' => $USER->getID ()),
				'limit'  => 1
			)
		);
		if ($user && isset($user[0]))
		{
			$user = $user[0];
		}

		$user['ACTIVE_CONTEXT_ID']       = $id;
		$user['ACTIVE_CONTEXT_EXTERNAL'] = ($id) ? 1 : 0;
		$user['ACTIVE_CONTEXT_UPDATED']  = date('d.m.Y H:i:s');

		//DebMes("setting external context: " . $id);
		$userID = $user['ID'];
		unset($user['ID']);
		//SQLUpdate('users', $user);
		Tables\UsersTable::update($userID, array("VALUES"=>$user));

		if ($id)
		{
			//execute pattern
			if (!$timeout)
				$timeout = 60;

			$ev = '';

			if ($timeout_code)
				$ev .= $timeout_code;

			if ($timeout_context_id)
			{
				$ev .= "context_activate_ext(" . (int)$timeout_context_id . ");";
			}
			else
			{
				$ev .= "context_clear();";
			}

			Jobs::setTimeOut('user_' . $USER->getID() . '_context_timeout', $ev, $timeout);
		}
		else
		{
			self::contextClear();
			Jobs::clearTimeOut('user_' . $USER->getID() . '_context_timeout');
		}
	}

	public static function contextTimeout($id, $user_id=null)
	{
		global $USER;
		if (is_null($user_id))
		{
			$user_id = $USER->getID();
		}

		//$user = SQLSelectOne("SELECT * FROM users WHERE ID = '" . (int)$user_id . "'");
		//$user =

		//$session->data['SITE_USER_ID'] = $user['ID'];

		//$context = SQLSelectOne("SELECT * FROM patterns WHERE ID = '" . (int)$id . "'");
		$context = Tables\PatternsTable::getList(
			array(
				'select' => array('ID','TIMEOUT_CONTEXT_ID','TIMEOUT_SCRIPT'),
				'filter' => array('ID'=>intval($id)),
				'limit' => 1
			)
		);
		if ($context && isset($context[0]))
		{
			$context = $context[0];
		}

		if (!$context['TIMEOUT_CONTEXT_ID'])
		{
			self::contextActivate(0,0,'',$user_id);
		}

		if ($context['TIMEOUT_SCRIPT'])
		{
			try
			{
				$code    = $context['TIMEOUT_SCRIPT'];
				$success = eval($code);

				if ($success === false)
				{
					//DebMes("Error in context timeout code: " . $code);
					//registerError('context_timeout_action', "Error in context timeout code: " . $code);
				}
			}
			catch (\Exception $e)
			{
				//DebMes('Error: exception ' . get_class($e) . ', ' . $e->getMessage() . '.');
				//registerError('context_timeout_action', get_class($e) . ', ' . $e->getMessage());
			}
		}

		if ($context['TIMEOUT_CONTEXT_ID'])
		{
			self::contextActivate((int)$context['TIMEOUT_CONTEXT_ID']);
		}
	}

	public static function addPattern($title, $options = array(), $overwrite = 0)
	{
		//$old = SQLSelectOne("SELECT ID FROM patterns WHERE TITLE LIKE '" . DBSafe($title) . "'");
		$old = Tables\PatternsTable::getOne(
			array(
				'select' => array('ID'),
				'filter' => array('TITLE'=>$title)
			)
		);

		if ($old['ID'])
		{
			if ($overwrite)
			{
				//SQLExec("DELETE FROM patterns WHERE ID = '" . $old['ID'] . "'");
				Tables\PatternsTable::delete($old['ID'],true);
			}
			else
			{
				return;
			}
		}

		$rec          = array();
		$rec['TITLE'] = $title;

		foreach ($options as $k => $v)
		{
			$rec[$k] = $v;
		}

		//SQLInsert('patterns', $rec);
		Tables\PatternsTable::add(array("VALUES"=>$rec));
	}
}