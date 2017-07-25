<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Месторасположения - Редактирование');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$pathTools = MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_TOOLS_ROOT');
if (isset($_POST['action']) && intval($_POST['action'])==1)
{
	$arLocation = array();
	if (isset($_POST['title']) && strlen($_POST['title'])>0)
	{
		$arLocation['TITLE'] = $_POST['title'];
	}
	$locationID = null;
	if (isset($_POST['id']) && intval($_POST['id'])>0)
	{
		$locationID = intval($_POST['id']);
	}
	if (isset($_POST['description']) && strlen($_POST['description'])>0)
	{
		$arLocation['DESCRIPTION'] = $_POST['description'];
	}
	else
	{
		$arLocation['DESCRIPTION'] = NULL;
	}
	if (isset($arLocation['TITLE']) && !is_null($locationID))
	{
		Tables\LocationsTable::update($locationID,array("VALUES"=>$arLocation));
		\MSergeev\Core\Lib\Buffer::setRefresh($curDir.'index.php');
	}
}
$arLocation = array();
if (isset($_REQUEST['id']) && intval($_REQUEST['id'])>0)
{
	$arLocation = Tables\LocationsTable::getOne(
		array(
			'select' => array('ID','TITLE','DESCRIPTION'),
			'filter' => array('ID'=>intval($_REQUEST['id']))
		)
	);
	if (!$arLocation)
	{
		$arLocation = array();
	}
}
?>
<ul class="breadcrumb">
	<li><a href="index.php">Начало</a></li>
	<span class="divider">/</span>
	<li class="active">Редактирование месторасположения</li>
</ul>
	<form action="" method="post" enctype="multipart/form-data" name="frmEdit" class="form-horizontal">
		<div class="form-group ">
			<label class="col-lg-4 control-label" for="inputTitle">Название:<span style="color:red">*</span></label>
			<div class="col-lg-5"><input type="text" class="form-control " name="title" value="<?=$arLocation['TITLE']?>" required="true"></div>
		</div>

		<div class="form-group ">
			<label class="col-lg-4 control-label" for="inputTitle">Описание:</label>
			<div class="col-lg-5"><input type="text" class="form-control " name="description" value="<?=$arLocation['DESCRIPTION']?>"></div>
		</div>

		<input type="hidden" name="action" value="1">
		<input type="hidden" name="id" value="<?=$arLocation['ID']?>">

		<div class="form-group">
			<div class="col-lg-offset-3 col-lg-5">
				<input type="submit" name="subm" class="btn btn-default btn-primary" value="Сохранить">
				&nbsp;
				<a href="index.php" class="btn btn-default">Отмена</a>
			</div>
		</div>
	</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
