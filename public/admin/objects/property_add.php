<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Свойства класса');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$classID = 0;
$bShowError = false;
$textError = '';
if (isset($_REQUEST['classID']))
{
	$classID = intval($_REQUEST['classID']);
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
	$arMethods = Tables\MethodsTable::getList(
		array(
			'select' => array('TITLE'),
			'filter' => array('CLASS_ID'=>$classID),
			'order' => array('TITLE'=>'ASC')
		)
	);
}
if (isset($_POST['action']))
{
	$arCheck = Tables\PropertiesTable::getList(
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
		$textError .= 'Невозможно создать свойство, так как свойство с данным именем уже существует<br>';
	}
	else
	{
		$propertyID = Lib\Objects::addClassProperty(
			$arClass['TITLE'],
			$_POST['title'],
			intval($_POST['keep_history']),
			$_POST['description']
		);
		if ($propertyID)
		{
			if ($_POST['onchange']!='')
			{
				Tables\PropertiesTable::update(
					$propertyID,
					array("VALUES"=>array(
						'ONCHANGE' => $_POST['onchange']
					))
				);
			}
			\MSergeev\Core\Lib\Buffer::setRefresh($curDir.'class_properties_list.php?show=success&id='.$classID);
		}
		else
		{
			$bShowError = true;
			$textError.='Возникла ошибка добавления свойства<br>';
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
$keep_history = 0;
if (isset($_POST['keep_history']))
{
	$keep_history = intval($_POST['keep_history']);
}
?>
<ol class="breadcrumb">
	<li><a href="<?=$curDir?>">Начало</a></li>
	<li class="active"><?=$arClass['TITLE']?></li>
</ol>
<ul class="nav nav-tabs">
	<li><a href="<?=$curDir?>class_edit.php?id=<?=$classID?>">Основное</a></li>
	<li class="active"><a href="<?=$curPage?>?id=<?=$classID?>">Свойства</a></li>
	<li><a href="<?=$curDir?>class_methods_list.php?id=<?=$classID?>">Методы</a></li>
	<li><a href="<?=$curDir?>class_objects_list.php?id=<?=$classID?>">Объекты</a></li>
</ul><br>
<?if($bShowError):?>
	<span class="text-danger"><?=$textError?></span>
<?endif;?>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Название:<span style="color:red;">*</span> <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4"><input type="text" class="form-control " name="title" value="<?=$title?>" required="true"></div>
	</div>
	<div class="form-group ">
		<label class="col-lg-4 control-label">Описание: <a href="http://majordomo.smartliving.ru/Hints/description?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4"><textarea name="description" rows="3" class="form-control"><?=$description?></textarea></div>
	</div>
	<div class="form-group ">
		<label class="col-lg-4 control-label">Запускать метод при изменении (не обязательно): <a href="http://majordomo.smartliving.ru/Hints/onchangemethod?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4">
			<select name="onchange" class="form-control">
				<option value=""></option>
				<?if($arMethods):?>
					<?foreach($arMethods as $arMethod):?>
						<option value="<?=$arMethod['TITLE']?>"<?if($_POST['onchange']==$arMethod['TITLE']):?> selected<?endif;?>><?=$arMethod['TITLE']?></option>
					<?endforeach;?>
				<?endif;?>
			</select>

		</div>
	</div>
	<div class="form-group ">
		<label class="col-lg-4 control-label">Хранить историю (дней): <a href="http://majordomo.smartliving.ru/Hints/keep_history?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4">
			<div class="form-inline">
				<div class="form-group col-lg-6">
					<input type="number" name="keep_history" value="<?=$keep_history?>" placeholder="0" min="0" step="1" size="10" class="form-control">
				</div>
				<div class="form-group col-lg-6">
					<p class="form-control-static">
						&nbsp;(0 = не хранить историю)
					</p>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-1 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Добавить">
			&nbsp;
			<a href="<?=$curDir?>class_properties_list.php?id=<?=$classID?>" class="btn btn-default">Отмена</a>
		</div>
	</div>
	<input type="hidden" name="classID" value="<?=$classID?>">
	<input type="hidden" name="action" value="1">
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
