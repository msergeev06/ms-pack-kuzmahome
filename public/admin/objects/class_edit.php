<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Редактирование класса');
use MSergeev\Packages\Kuzmahome\Tables;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$bShowSuccess = false;
$classID = 0;
if (isset($_REQUEST['show']))
{
	if ($_REQUEST['show']=='success')
	{
		$bShowSuccess = true;
	}
}
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
	if ($_POST['title']==$arClass['TITLE'])
	{
		//Сохраняем изменения
		Tables\ClassesTable::update(
			$classID,
			array("VALUES"=>array(
				'TITLE'=>$_POST['title'],
				'DESCRIPTION' => $_POST['description'],
				'PARENT_ID' => $_POST['parent_id']
			))
		);
		$arClass['TITLE'] = $_POST['title'];
		$arClass['DESCRIPTION'] = $_POST['description'];
		$arClass['PARENT_ID'] = $_POST['parent_id'];
		$bShowSuccess = true;
	}
	else
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
			?><span class="text-danger">Невозможно переименовать класс в <?=$_POST['title']?>, так как класс с таким именем уже существует</span><br><?
		}
		else
		{
			$bNoRename = false;
			$arCheck = Tables\PropertiesTable::getList(
				array(
					'select' => array('ID'),
					'filter' => array('CLASS_ID'=>$classID),
					'limit' => 1
				)
			);
			if ($arCheck && isset($arCheck[0]))
			{
				$arCheck = $arCheck[0];
			}
			if ($arCheck)
			{
				$bNoRename = true;
			}
			$arCheck = Tables\MethodsTable::getList(
				array(
					'select' => array('ID'),
					'filter' => array('CLASS_ID'=>$classID),
					'limit' => 1
				)
			);
			if ($arCheck && isset($arCheck[0]))
			{
				$arCheck = $arCheck[0];
			}
			if ($arCheck)
			{
				$bNoRename = true;
			}
			$arCheck = Tables\ObjectsTable::getList(
				array(
					'select' => array('ID'),
					'filter' => array('CLASS_ID'=>$classID),
					'limit' => 1
				)
			);
			if ($arCheck && isset($arCheck[0]))
			{
				$arCheck = $arCheck[0];
			}
			if ($arCheck)
			{
				$bNoRename = true;
			}
			if ($bNoRename)
			{
				?><span class="text-danger">Невозможно переименовать класс так как у него уже существуют свойства, методы и/или объекты</span><br><?
			}
			else
			{
				//Сохраняем изменения
				Tables\ClassesTable::update(
					$classID,
					array("VALUES"=>array(
						'TITLE'=>$_POST['title'],
						'DESCRIPTION' => $_POST['description'],
						'PARENT_ID' => $_POST['parent_id']
					))
				);
				$arClass['TITLE'] = $_POST['title'];
				$arClass['DESCRIPTION'] = $_POST['description'];
				$arClass['PARENT_ID'] = $_POST['parent_id'];
				$bShowSuccess = true;
			}

		}
	}
}
$arClasses = Tables\ClassesTable::getList(
	array(
		'select' => array('ID','TITLE'),
		'order' => array('TITLE'=>'ASC')
	)
);
?>
<ol class="breadcrumb">
	<li><a href="<?=$curDir?>">Начало</a></li>
	<li class="active"><?=$arClass['TITLE']?></li>
</ol>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?=$curPage?>?id=<?=$classID?>">Основное</a></li>
	<li><a href="<?=$curDir?>class_properties_list.php?id=<?=$classID?>">Свойства</a></li>
	<li><a href="<?=$curDir?>class_methods_list.php?id=<?=$classID?>">Методы</a></li>
	<li><a href="<?=$curDir?>class_objects_list.php?id=<?=$classID?>">Объекты</a></li>
</ul>
<?if($bShowSuccess):?><span class="text-success">Данные сохранены</span><?endif;?>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Родительский класс: <a href="http://majordomo.smartliving.ru/Hints/parent_class?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4">
			<select name="parent_id" class="form-control">
				<option value="0"<?if($arClass['PARENT_ID']==0):?> selected<?endif;?>>- no -</option>
				<?foreach($arClasses as $ar_class):?>
					<option value="<?=$ar_class['ID']?>"<?if($arClass['PARENT_ID']>0 && $arClass['PARENT_ID']==$ar_class['ID']):?> selected<?endif;?>><?=$ar_class['TITLE']?></option>
				<?endforeach;?>
			</select>
		</div>
	</div>
	<div class="form-group ">
		<label class="col-lg-4 control-label">Название:<span style="color:red;">*</span> <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4"><input type="text" class="form-control " name="title" value="<?=$arClass['TITLE']?>" required="true"></div>
	</div>
	<div class="form-group ">
		<label class="col-lg-4 control-label">Описание: <a href="http://majordomo.smartliving.ru/Hints/description?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-4"><textarea name="description" rows="3" class="form-control"><?=$arClass['DESCRIPTION']?></textarea></div>
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
<a href="#" class="btn btn-default" title="Экспорт"><i class="glyphicon glyphicon-export"></i> Экспорт Класса и Объектов</a><br><br>
<a href="#" class="btn btn-default" title="Экспорт"><i class="glyphicon glyphicon-export"></i> Экспорт Класса (без объектов)</a>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
