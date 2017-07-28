<?php
include_once('/var/www/kuzmahome/config.php');
use MSergeev\Core\Lib as CoreLib;

$documentRoot = CoreLib\Config::getConfig('DOCUMENT_ROOT');

//Устанавливаем флаг перезагрузки, чтобы автоматически запускаемые скрипты не исполнялись
if (!file_exists($documentRoot.'reboot'))
{
	$f1 = fopen($documentRoot.'reboot','w');
	fwrite($f1,date('Y-m-d H:i:s'));
	fclose($f1);
}

CoreLib\Events::runEvents('kuzmahome','OnStartUp');

//Убираем флаг перезагрузки
if (file_exists($documentRoot.'reboot'))
{
	unlink($documentRoot.'reboot');
}

