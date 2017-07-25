<?php

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Core\Entity;

//Очищаем таблицу ms_owm_forecast, записи которой старше 30 дней
$query = new Entity\Query('delete');
$sqlHelper = new CoreLib\SqlHelper('ms_owm_forecast');
$sql = "DELETE FROM\n\t"
	.$sqlHelper->wrapTableQuotes()."\nWHERE\n\t"
	.$sqlHelper->wrapFieldQuotes('DATETIME_FROM')." < '"
	.date('Y-m-d 00:00:00',strtotime('-30 days'))."'";
$query->setQueryBuildParts($sql);
$query->exec();


