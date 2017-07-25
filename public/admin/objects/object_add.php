<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Редактирование объекта класса');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
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
$arRooms = false;
if (isset($_POST['action']))
{
	if ($_POST['class_id']!='')
	{
		$classID = Lib\Objects::addClass($_POST['class_id']);
		if (intval($classID)>0)
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
				$textError .= 'Невозможно создать объект '.$_POST['title'].', так как объект с данным именем уже существует<br>';
			}
			else
			{
				//Сохраняем изменения
				$objectID = Lib\Objects::addClassObject(
					$_POST['class_id'],
					$_POST['title'],
					$_POST['description'],
					intval($_POST['room_id'])
				);
				\MSergeev\Core\Lib\Buffer::setRefresh($curDir.'class_object_edit.php?show=success&classID='.$classID.'&id='.$objectID);
			}
		}
		else
		{
			$bShowError = true;
			$bShowSuccess = false;
			$textError .= 'Невозможно создать объект не выбрав класс<br>';
		}
	}
	else
	{
		$bShowError = true;
		$bShowSuccess = false;
		$textError .= 'Невозможно создать объект не выбрав класс<br>';
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
$room_id = 0;
if (isset($_POST['room_id']))
{
	$room_id = $_POST['room_id'];
}
?>

<?if($bShowError):?>
	<span class="text-danger"><?=$textError?></span><br><br>
<?endif;?>
<?if($bShowSuccess):?>
	<span class="text-success">Данные сохранены</span><br><br>
<?endif;?>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Класс:<span style="color:red;">*</span>  <a href="http://majordomo.smartliving.ru/Hints/object_class?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4">
			<select name="class_id" class="form-control " required="true">
				<option value="">select</option>
				<?if($arClasses):?>
					<?foreach($arClasses as $ar_class):?>
						<option value="<?=$ar_class['TITLE']?>"><?=$ar_class['TITLE']?></option>
					<?endforeach;?>
				<?endif;?>
			</select>
			</select>
		</div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label">Название:<span style="color:red;">*</span>  <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4"><input type="text" class="form-control " name="title" value="<?=$title?>" required="true"></div>
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
						<option value="<?=$arRoom['ID']?>"<?if($room_id==$arRoom['ID']):?> selected<?endif;?>><?=$arRoom['TITLE']?></option>
					<?endforeach;?>
				<?endif;?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-offset-1 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Сохранить">
			&nbsp;
			<a href="<?=$curDir?>" class="btn btn-default">Отмена</a>
		</div>
	</div>


	<input type="hidden" name="id" value="<?=$classID?>">
	<input type="hidden" name="action" value="1">
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
