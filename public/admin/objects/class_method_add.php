<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Добавление метода класса');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$classID = 0;
$bShowError = false;
$textError = '';
if (isset($_REQUEST['id']))
{
	$classID = intval($_REQUEST['id']);
}
if ($classID==0)
{
	?><span class="text-danger">ID класса не может равняться 0 (нулю)</span><?
	die();
}
else
{
	$arClass = Tables\ClassesTable::getList(
		array(
			'select' => array('ID','TITLE','DESCRIPTION','PARENT_ID'),
			'filter' => array('ID' => $classID),
			'limit' => 1
		)
	);
	if ($arClass && isset($arClass[0]))
	{
		$arClass = $arClass[0];
	}
	if (!$arClass)
	{
		?><span class="text-danger">Ошибка загрузки данных класса</span><?
		die();
	}
}
if (isset($_POST['action']))
{
	$arCheck = Tables\MethodsTable::getList(
		array(
			'select' => array('ID'),
			'filter' => array(
				'TITLE'=>$_POST['title'],
				'CLASS_ID' => $classID
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
		$textError .= 'Невозможно создать метод, так как метод с данным именем уже существует<br>';
	}
	else
	{
		$methodID = Lib\Objects::addClassMethod(
			$arClass['TITLE'],
			$_POST['title'],
			'',
			$_POST['description']
		);
		if ($methodID)
		{
			\MSergeev\Core\Lib\Buffer::setRefresh($curDir.'class_method_edit.php?show=success&classID='.$classID.'&id='.$methodID);
		}
		else
		{
			$bShowError = true;
			$textError.='Возникла ошибка добавления метода<br>';
		}
	}
}
$title = '';
if (isset($_POST['title']))
{
	$title = $_POST['title'];
}
$description = '';
if (isset($_POST['description']))
{
	$description = $_POST['description'];
}
?>
<ol class="breadcrumb">
	<li><a href="<?=$curDir?>">Начало</a></li>
	<li class="active"><?=$arClass['TITLE']?></li>
</ol>
<ul class="nav nav-tabs">
	<li><a href="<?=$curDir?>class_edit.php?id=<?=$classID?>">Основное</a></li>
	<li><a href="<?=$curDir?>class_properties_list.php?id=<?=$classID?>">Свойства</a></li>
	<li class="active"><a href="<?=$curDir?>class_methods_list.php?id=<?=$classID?>">Методы</a></li>
	<li><a href="<?=$curDir?>class_objects_list.php?id=<?=$classID?>">Объекты</a></li>
</ul><br>
<?if($bShowError):?>
	<span class="text-danger"><?=$textError?></span>
<?endif;?>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" id="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Название:<span style="color:red;">*</span> <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="title" value="<?=$title?>" required="true"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label">Описание <a href="http://majordomo.smartliving.ru/Hints/description?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<textarea name="description" id="description" rows="3" cols="100" class="form-control"><?=$description?></textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-offset-1 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Добавить">
			&nbsp;
			<a href="<?=$curDir?>class_methods_list.php?id=<?=$classID?>" class="btn btn-default">Отмена</a>
		</div>
	</div>

	<input type="hidden" name="id" value="<?=$classID?>">
	<input type="hidden" name="action" value="1">
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
