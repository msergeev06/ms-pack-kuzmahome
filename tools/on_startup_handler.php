<?php

$lastStartedTime = \MSergeev\Packages\Kuzmahome\Lib\Objects::getGlobal('System.propStartedTime');
$nowTime = time();
\MSergeev\Packages\Kuzmahome\Lib\Objects::setGlobal('System.propStartedTime',$nowTime);
$lostTime = $nowTime - $lastStartedTime;
if ($lostTime>3600)
{
	\MSergeev\Core\Lib\Events::runEvents('kuzmahome','OnNewHour');
}
if ($lostTime>86400)
{
	\MSergeev\Core\Lib\Events::runEvents('kuzmahome','OnNewDay');
}

\MSergeev\Packages\Kuzmahome\Lib\Objects::callMethod('System.OnStartUp');
