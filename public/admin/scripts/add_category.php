<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Скрипты - Добавление категории');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$pathTools = MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_TOOLS_ROOT');
\MSergeev\Core\Lib\Plugins::includeCodeMirror();
if (!$view = $USER->getUserCookie('admin_scripts_page_view'))
{
	$view = 'list';
	$USER->setUserCookie('admin_scripts_page_view','list');
}
if (isset($_REQUEST['view']) && ($_REQUEST['view']=='list' || $_REQUEST['view']=='category'))
{
	$view = $_REQUEST['view'];
	$USER->setUserCookie('admin_scripts_page_view',$view);
}
if (isset($_POST['action']) && intval($_POST['action'])==1)
{
	$arScript = array();
	if (isset($_POST['title']) && strlen($_POST['title'])>0)
	{
		Tables\ScriptsCategoriesTable::add(array("VALUES"=>array('TITLE'=>$_POST['title'])));
		\MSergeev\Core\Lib\Buffer::setRefresh($curDir.'index.php');
	}
}
?>
<ul class="nav nav-tabs">
	<li<?=(($view=='list')?' class="active"':'')?>><a href="index.php?view=list">Сценарии</a></li>
	<li<?=(($view=='category')?' class="active"':'')?>><a href="index.php?view=category">Категории</a></li>
</ul>
<br>
<ul class="breadcrumb">
	<li><a href="<?=$curDir?>">Начало</a></li>
	<span class="divider">/</span>
	<li class="active">Добавление категории</li>
</ul>
<br>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" id="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Название (можно по-русски):<span style="color:red">*</span> </label>
		<div class="col-lg-5"><input type="text" class="form-control " name="title" value="" required="true"></div>
	</div>

	<input type="hidden" name="action" value="1">

	<div class="form-group">
		<div class="col-lg-offset-1 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Добавить">
			&nbsp;
			<a href="index.php" class="btn btn-default">Отмена</a>
		</div>
	</div>

	&nbsp;
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
