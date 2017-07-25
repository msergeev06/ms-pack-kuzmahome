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
if (isset($_REQUEST['delete']))
{
	$obj = Lib\Objects::getObject($arClass['TITLE'].'.'.$arObject['TITLE']);
	$deleteID = $obj->getPropertyByName($_REQUEST['delete']);
	if ($deleteID)
	{
		Tables\PropertiesTable::delete($deleteID,true);
	}
}
$arProperties = Tables\PropertiesTable::getList(
	array(
		'select' => array('ID','TITLE','DESCRIPTION','CLASS_ID','OBJECT_ID'),
		'filter' => array(
			'CLASS_ID' => $classID,
			'OBJECT_ID' => $objectID
		),
		'filter_logic' => 'OR',
		'order' => array('OBJECT_ID'=>'ASC','TITLE'=>'ASC')
	)
);
$arPropValues = array();
foreach ($arProperties as $arProp)
{
	$arPropValues[$arObject['TITLE'].'.'.$arProp['TITLE']] = Lib\Objects::getGlobal($arObject['TITLE'].'.'.$arProp['TITLE']);
}
if (isset($_POST['action']))
{
	if (isset($_POST['new_property']) && strlen($_POST['new_property'])>0)
	{
		if (!isset($arPropValues[$arObject['TITLE'].'.'.$_POST['new_property']]))
		{
			Lib\Objects::setGlobal(
				$arObject['TITLE'].'.'.$_POST['new_property'],
				$_POST['new_value']
			);
		}
	}
	foreach ($_POST as $key=>$value)
	{
		if (preg_match('/('.$arObject['TITLE'].')_(.*)/',$key,$m))
		{
			if ($_POST[$m[0]]!=$arPropValues[$m[1].'.'.$m[2]])
			{
				Lib\Objects::setGlobal($m[1].'.'.$m[2],$_POST[$m[0]]);
			}
		}
	}
	$arProperties = Tables\PropertiesTable::getList(
		array(
			'select' => array('ID','TITLE','DESCRIPTION','CLASS_ID','OBJECT_ID'),
			'filter' => array(
				'CLASS_ID' => $classID,
				'OBJECT_ID' => $objectID
			),
			'filter_logic' => 'OR',
			'order' => array('OBJECT_ID'=>'ASC','TITLE'=>'ASC')
		)
	);
	$arPropValues = array();
	foreach ($arProperties as $arProp)
	{
		$arPropValues[$arObject['TITLE'].'.'.$arProp['TITLE']] = Lib\Objects::getGlobal($arObject['TITLE'].'.'.$arProp['TITLE']);
	}
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
	<li><a href="<?=$curDir?>class_object_edit.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>">Детали</a></li>
	<li class="active"><a href="<?=$curPage?>?classID=<?=$classID?>&id=<?=$objectID?>">Свойства</a></li>
	<li><a href="<?=$curDir?>object_methods_list.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>">Методы</a></li>
</ul>
<?if($bShowError):?>
	<span class="text-danger"><?=$textError?></span><br><br>
<?endif;?>
<?if($bShowSuccess):?>
	<span class="text-success">Данные сохранены</span><br><br>
<?endif;?>
<form action="" method="post">
	<table border="0" class="table table-bordered">
		<tbody>
		<?if($arProperties):?>
			<?foreach($arProperties as $arProp):?>
			<tr>
				<td valign="top">
					<?if(intval($arProp['OBJECT_ID'])>0):?>
						<?=$arObject['TITLE']?>.<?=$arProp['TITLE']?><br>
					<?else:?>
						<b><?=$arObject['TITLE']?>.<?=$arProp['TITLE']?></b><br>
					<?endif;?>
					<i><?=$arProp['DESCRIPTION']?></i>
				</td>
				<td valign="top">
					<textarea name="<?=$arObject['TITLE'].'_'.$arProp['TITLE']?>" rows="2" cols="50" class="span5"><?if($arPropValues[$arObject['TITLE'].'.'.$arProp['TITLE']]!==false):?><?=$arPropValues[$arObject['TITLE'].'.'.$arProp['TITLE']]?><?endif;?></textarea>
				</td>
				<td valign="top">
					<?if(intval($arProp['OBJECT_ID'])>0):?>
						<a href="<?=$curPage?>?delete=<?=$arProp['TITLE']?>&classID=<?=$classID?>&id=<?=$objectID?>" onclick="return confirm('Вы уверены, что хотите удалить свойство <?=$arProp['TITLE']?>?')" title="Удалить" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-remove"></i></a>
					<?endif;?>
					&nbsp;
				</td>
			</tr>
			<?endforeach;?>
		<?endif;?>

			<tr>
				<td valign="top" colspan="4">
					Добавить новое свойство:
				</td>
			</tr>
			<tr>
				<td valign="top"><?=$arObject['TITLE']?>.<input type="text" name="new_property" value=""></td>
				<td valign="top"><input type="text" name="new_value" value=""></td>
				<td valign="top">&nbsp;</td>
			</tr>
			<tr>
				<td valign="top" colspan="4">
					<input type="submit" name="submit" value="Обновить" class="btn btn-primary">
				</td>
			</tr>

			<input type="hidden" name="classID" value="<?=$classID?>">
			<input type="hidden" name="id" value="<?=$objectID?>">
			<input type="hidden" name="action" value="1">
		</tbody>
	</table>
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
