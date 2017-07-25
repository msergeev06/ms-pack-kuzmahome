<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Добавление класса');
use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = CoreLib\Tools::getCurPath();
$curDir = CoreLib\Tools::getCurDir();
$bShowForm = true;
$bShowError = false;
if (isset($_POST['action']))
{
	$arCheck = Tables\ClassesTable::getList(
		array(
			'select' => array('ID'),
			'filter' => array(
				'TITLE' => $_POST['title']
			),
			'limit' => 1
		)
	);
	if ($arCheck && isset($arCheck[0]))
	{
		$arCheck = $arCheck[0];
	}
	if ($arCheck)
	{
		$bShowError = true;
	}
	else
	{
		$newClassID = Lib\Objects::addClass($_POST['title'], intval($_POST['parent_id']), $_POST['description']);
		CoreLib\Buffer::setRefresh($curDir.'class_edit.php?show=success&id='.$newClassID);
		$bShowForm = false;
	}
}

if ($bShowForm)
{
	$arClasses = Tables\ClassesTable::getList(
		array(
			'select' => array('ID','TITLE'),
			'order' => array('TITLE'=>'ASC')
		)
	);
	if ($bShowError)
	{
		?><p class="text-danger">Невозможно добавить класс, так как класс с таким именем уже существует!</p><?
	}
?>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Родительский класс: <a href="http://majordomo.smartliving.ru/Hints/parent_class?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4">
			<select name="parent_id" class="form-control">
				<option value="0"<?if(!isset($_POST['parent_id'])):?> selected<?endif;?>>- no -</option>
				<?foreach($arClasses as $arClass):?>
					<option value="<?=$arClass['ID']?>"<?if(isset($_POST['parent__id']) && $_POST['parent_id']==$arClass['ID']):?> selected<?endif;?>><?=$arClass['TITLE']?></option>
				<?endforeach;?>
			</select>
		</div>
	</div>
	<div class="form-group ">
		<label class="col-lg-4 control-label">Название:<span style="color:red;">*</span> <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4"><input type="text" class="form-control " name="title" value="<?if(isset($_POST['title'])):?><?=$_POST['title']?><?endif;?>" required="true"></div>
	</div>
	<div class="form-group ">
		<label class="col-lg-4 control-label">Описание: <a href="http://majordomo.smartliving.ru/Hints/description?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4"><textarea name="description" rows="3" class="form-control"><?if(isset($_POST['description'])):?><?=$_POST['description']?><?endif;?></textarea></div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-1 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Добавить">
			&nbsp;
			<a href="<?=$curDir?>" class="btn btn-default">Отмена</a>
		</div>
	</div>
	<input type="hidden" name="action" value="1">
</form>
<?
}
$curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
