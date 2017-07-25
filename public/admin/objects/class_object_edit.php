<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Редактирование объекта класса');
use MSergeev\Packages\Kuzmahome\Tables;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$classID = 0;
$bShowError = false;
$bShowSuccess = false;
if (isset($_REQUEST['show']) && $_REQUEST['show']=='success')
{
	$bShowSuccess = true;
}
if (isset($_REQUEST['classID']))
{
	$classID = intval($_REQUEST['classID']);
}
if (isset($_POST['class_id']) && intval($_POST['class_id'])>0)
{
	$classID = intval($_POST['class_id']);
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
$objectID = 0;
if (isset($_REQUEST['id']))
{
	$objectID = intval($_REQUEST['id']);
}
if ($objectID==0)
{
	?><span class="text-danger">ID объекта не может равняться 0 (нулю)</span><?
	die();
}
else
{
	$arObject = Tables\ObjectsTable::getList(
		array(
			'select' => array('ID','TITLE','DESCRIPTION','ROOM_ID'),
			'filter' => array('ID' => $objectID),
			'limit' => 1
		)
	);
	if ($arObject && isset($arObject[0]))
	{
		$arObject = $arObject[0];
	}
	if (!$arObject)
	{
		?><span class="text-danger">Ошибка загрузки данных объекта</span><?
		die();
	}
}
$arClasses = Tables\ClassesTable::getList(
	array(
		'select' => array('ID','TITLE'),
		'order' => array('TITLE')
	)
);
/*
$arRooms = Tables\RoomsTable::getList(
	array(
		'select' => array('ID','TITLE'),
		'order' => array('TITLE')
	)
);
*/
$arRooms = Tables\LocationsTable::getList(
	array(
		'select' => array('ID','TITLE','DESCRIPTION'),
		'order' => array('TITLE'=>'ASC')
	)
);
if (isset($_POST['action']))
{
	if ($_POST['title']==$arObject['TITLE'])
	{
		//Сохраняем изменения
		Tables\ObjectsTable::update(
			$objectID,
			array("VALUES"=>array(
				'TITLE' => $_POST['title'],
				'CLASS_ID' => $_POST['class_id'],
				'DESCRIPTION' => $_POST['description'],
				'ROOM_ID' => intval($_POST['room_id'])
			))
		);
		$bShowSuccess = true;
	}
	else
	{
		$arCheck = Tables\ObjectsTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array('TITLE'=>$_POST['title']),
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
			$bShowSuccess = false;
			$textError .= 'Невозможно изменить имя объекта, так как объект с данным именем уже существует<br>';
		}
		else
		{
			//Сохраняем изменения
			Tables\ObjectsTable::update(
				$objectID,
				array("VALUES"=>array(
					'TITLE' => $_POST['title'],
					'CLASS_ID' => $_POST['class_id'],
					'DESCRIPTION' => $_POST['description'],
					'ROOM_ID' => intval($_POST['room_id'])
				))
			);
			$bShowSuccess = true;
		}
	}
}
$title = $arObject['TITLE'];
if (isset($_POST['title']))
{
	$title = $_POST['title'];
}
$description = $arObject['DESCRIPTION'];
if (isset($_POST['description']))
{
	$description = $_POST['description'];
}
$room_id = $arObject['ROOM_ID'];
if (isset($_POST['room_id']))
{
	$room_id = $_POST['room_id'];
}
?>
<ol class="breadcrumb">
	<li><a href="<?=$curDir?>">Начало</a></li>
	<li class="active"><?=$arClass['TITLE']?></li>
</ol>
<ul class="nav nav-tabs">
	<li><a href="<?=$curDir?>class_edit.php?id=<?=$classID?>">Основное</a></li>
	<li><a href="<?=$curDir?>class_properties_list.php?id=<?=$classID?>">Свойства</a></li>
	<li><a href="<?=$curDir?>class_methods_list.php?id=<?=$classID?>">Методы</a></li>
	<li class="active"><a href="<?=$curDir?>class_objects_list.php?id=<?=$classID?>">Объекты</a></li>
</ul><br>
<ol class="breadcrumb">
	<li class="active">Объект: <?=$arObject['TITLE']?></li>
</ol>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?=$curPage?>?classID=<?=$classID?>&id=<?=$objectID?>">Детали</a></li>
	<li><a href="<?=$curDir?>object_properties_list.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>">Свойства</a></li>
	<li><a href="<?=$curDir?>object_methods_list.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>">Методы</a></li>
</ul>
<?if($bShowError):?>
	<span class="text-danger"><?=$textError?></span><br><br>
<?endif;?>
<?if($bShowSuccess):?>
	<span class="text-success">Данные сохранены</span><br><br>
<?endif;?>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Название:<span style="color:red;">*</span>  <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4"><input type="text" class="form-control " name="title" value="<?=$title?>" required="true"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label">Класс:<span style="color:red;">*</span>  <a href="http://majordomo.smartliving.ru/Hints/object_class?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4">
			<select name="class_id" class="form-control " required="true">
				<option value="">select</option>
				<?if($arClasses):?>
					<?foreach($arClasses as $ar_class):?>
						<option value="<?=$ar_class['ID']?>"<?if($classID==$ar_class['ID']):?> selected<?endif;?>><?=$ar_class['TITLE']?></option>
					<?endforeach;?>
				<?endif;?>
				</select>
			</select>
		</div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label">Описание:  <a href="http://majordomo.smartliving.ru/Hints/description?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4"><textarea name="description" rows="3" class="form-control"><?=$description?></textarea></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label">Местоположение:  <a href="http://majordomo.smartliving.ru/Hints/location?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4">
			<select name="room_id" class="form-control">
				<option value="0">-</option>
				<?if($arRooms):?>
					<?foreach($arRooms as $arRoom):?>
						<option value="<?=$arRoom['ID']?>"<?if($room_id==$arRoom['ID']):?> selected<?endif;?>>[<?=$arRoom['TITLE']?>] <?=$arRoom['DESCRIPTION']?></option>
					<?endforeach;?>
				<?endif;?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-offset-1 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Сохранить">
			&nbsp;
			<a href="<?=$curDir?>class_objects_list.php?id=<?=$classID?>" class="btn btn-default">Отмена</a>
			<a class="btn btn-default" href="<?=$curPage?>?clone=object&classID=<?=$classID?>&id=<?=$objectID?>" onclick="return confirm('Вы уверены? Пожалуйста, подтвердите операцию.')">Создать копию (клонировать)</a>
		</div>
	</div>


	<input type="hidden" name="id" value="<?=$objectID?>">
	<input type="hidden" name="classID" value="<?=$classID?>">
	<input type="hidden" name="action" value="1">
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
