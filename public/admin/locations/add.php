<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Месторасположения - Добавление');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$pathTools = MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_TOOLS_ROOT');
$bShowForm = true;
if (isset($_POST['action']) && intval($_POST['action'])==1)
{
	$arLocation = array();
	if (isset($_POST['title']) && strlen($_POST['title'])>0)
	{
		$arLocation['TITLE'] = $_POST['title'];
		if (isset($_POST['description']) && strlen($_POST['description'])>0)
		{
			$arLocation['DESCRIPTION'] = $_POST['description'];
		}
		$newLocationID = Tables\LocationsTable::add(array("VALUES"=>$arLocation))->getInsertId();
		if ($newLocationID)
		{
			\MSergeev\Core\Lib\Buffer::setRefresh($curDir.'index.php');
			$bShowForm = false;
		}
	}
}
?>
<ul class="breadcrumb">
	<li><a href="index.php">Начало</a></li>
	<span class="divider">/</span>
	<li class="active">Новая запись</li>
</ul>
<?if($bShowForm):?>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Название:<span style="color:red">*</span></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="title" value="" required="true"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Описание:</label>
		<div class="col-lg-5"><input type="text" class="form-control " name="description" value=""></div>
	</div>

	<input type="hidden" name="action" value="1">

	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-5">
			<input type="submit" name="subm" class="btn btn-default btn-primary" value="Добавить">
			&nbsp;
			<a href="index.php" class="btn btn-default">Отмена</a>
		</div>
	</div>
</form>
<?endif;?>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
