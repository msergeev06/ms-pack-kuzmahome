<?php
use MSergeev\Packages\Kuzmahome\Lib;
global $DB;
$scriptsDir = \MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_ROOT').'scripts/';

//Сохраняем дневные логи
Lib\Logs::saveDailyLogs();
//Подключаем скрипт Удаления лишних файлов
include($scriptsDir.'clear_files.php');
//Подключаем скрипт Удаления лишних записей в DB
include($scriptsDir.'clear_old_db_rows.php');


callMethod('System.OnNewDay');


//Создаем ежедневный бекап базы msergeev
exec($DB->getDumpCommand(\MSergeev\Core\Lib\Config::getConfig('DIR_BACKUP_DB'),'daily'));
//Создаем ежедневный бекап базы db_terminal
exec($DB->getDumpCommand('/var/www/backup_db/','daily',null,array(),true,true,false,'db_terminal'));