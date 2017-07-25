<?php

use MSergeev\Packages\Kuzmahome\Lib;

if (!function_exists('addClass'))
{
	function addClass ($class_name, $parent_class = '', $description = '')
	{
		return Lib\Objects::addClass ($class_name, $parent_class, $description);
	}
}

if (!function_exists('addClassMethod'))
{
	function addClassMethod ($class_name, $method_name, $code = '')
	{
		return Lib\Objects::addClassMethod ($class_name, $method_name, $code);
	}
}

if (!function_exists('addClassProperty'))
{
	function addClassProperty ($class_name, $property_name, $keep_history = 0)
	{
		return Lib\Objects::addClassProperty ($class_name, $property_name, $keep_history);
	}
}

if (!function_exists('addClassObject'))
{
	function addClassObject ($class_name, $object_name)
	{
		return Lib\Objects::addClassObject ($class_name, $object_name);
	}
}

if (!function_exists('getValueIdByName'))
{
	function getValueIdByName ($object_name, $property)
	{
		return Lib\Objects::getValueIdByName ($object_name, $property);
	}
}

if (!function_exists('addLinkedProperty'))
{
	function addLinkedProperty ($object, $property, $module)
	{
		return Lib\Objects::addLinkedProperty ($object, $property, $module);
	}
}

if (!function_exists('removeLinkedProperty'))
{
	function removeLinkedProperty ($object, $property, $module)
	{
		return Lib\Objects::removeLinkedProperty ($object, $property, $module);
	}
}

if (!function_exists('getObject'))
{
	function getObject ($name)
	{
		return Lib\Objects::getObject ($name);
	}
}

if (!function_exists('getGlobal'))
{
	function getGlobal ($varname)
	{
		return Lib\Objects::getGlobal ($varname);
	}
}

if (!function_exists('setGlobal'))
{
	function setGlobal ($varname, $value, $no_linked = 0)
	{
		return Lib\Objects::setGlobal ($varname, $value, $no_linked);
	}
}

if (!function_exists('callMethod'))
{
	function callMethod ($method_name, $params = 0)
	{
		return Lib\Objects::callMethod ($method_name, $params);
	}
}

/* SHORT ALIAS */
if (!function_exists('sg'))
{
	function sg ($varname, $value, $no_linked = 0)
	{
		return \setGlobal ($varname, $value, $no_linked);
	}
}

if (!function_exists('gg'))
{
	function gg ($varname)
	{
		return \getGlobal ($varname);
	}
}

if (!function_exists('cm'))
{
	function cm ($method_name, $params = 0)
	{
		return \callMethod ($method_name, $params);
	}
}
/*
function rs($script_id, $params = 0)
{
	return \runScript($script_id, $params);
}*/



