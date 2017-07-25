<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

class Player
{
	var $path;
	var $host;

	public function __construct ()
	{
	}

	public function play ($path, $host='localhost')
	{
		$this->path = $path;
		$this->host = $host;

		return false;
	}

}