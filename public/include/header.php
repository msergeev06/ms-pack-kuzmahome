<?php

include_once ($_SERVER["DOCUMENT_ROOT"]."/kuzmahome/config.php");
$curPath = \MSergeev\Core\Lib\Tools::getCurDir();
if (preg_match("/.*\/admin\/.*/",$curPath,$m))
{
	\MSergeev\Core\Lib\Loader::setTemplate('kuzmahome','admin');
}
__include_once(MSergeev\Core\Lib\Loader::getTemplate("kuzmahome")."header.php");

MSergeev\Core\Lib\Buffer::addCSS(MSergeev\Core\Lib\Loader::getTemplate("kuzmahome")."css/style.css");
MSergeev\Core\Lib\Buffer::addJS(MSergeev\Core\Lib\Config::getConfig("CORE_ROOT")."js/jquery-1.11.3.min.js");
MSergeev\Core\Lib\Buffer::addJS(MSergeev\Core\Lib\Loader::getTemplate("kuzmahome")."js/script.js");
\MSergeev\Core\Lib\Plugins::includeBootstrapCss();
\MSergeev\Core\Lib\Plugins::includeBootstrapJs();

//$path=MSergeev\Core\Lib\Loader::getSitePublic('kuzmahome');
