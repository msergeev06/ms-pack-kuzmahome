<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Объекты класса');
use MSergeev\Packages\Kuzmahome\Tables;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$classID = 0;
/*$bShowSuccess = false;
if (isset($_REQUEST['show']) && $_REQUEST['show']=='success')
{
	$bShowSuccess = true;
}*/
if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='delete'
	&& isset($_REQUEST['objectID']) && intval($_REQUEST['objectID'])>0
)
{
	Tables\ObjectsTable::delete(intval($_REQUEST['objectID']),true);
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
	$arObjects = Tables\ObjectsTable::getList(
		array(
			'select' => array('ID','TITLE','DESCRIPTION','ROOM_ID','ROOM_ID.TITLE'=>'ROOM_TITLE','ROOM_ID.DESCRIPTION'=>'ROOM_DESCRIPTION'),
			'filter' => array('CLASS_ID'=>$classID),
			'order' => array('TITLE'=>'ASC')
		)
	);
	if (!$arObjects)
	{
		$bShowObjects = false;
	}
	else
	{
		$bShowObjects = true;
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
	<li class="active"><a href="<?=$curPage?>?id=<?=$classID?>">Объекты</a></li>
</ul><br>
<?if($bShowSuccess):?>
	<span class="text-success">Данные сохранены</span><br><br>
<?endif;?>
<a href="<?=$curDir?>class_object_add.php?id=<?=$classID?>" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i> Добавить новый объект</a><br>
<table class="table table-bordered">
<tbody><tr>
	<td align="center">
		<b>Название</b>
	</td>
	<td align="center">
		<b>Класс</b>
	</td>
	<td align="center">
		<b>Описание</b>
	</td>
	<td align="center">
		<b>Местоположение</b>
	</td>
	<td>&nbsp;</td>
</tr>
<?if($bShowObjects):?>
	<?foreach($arObjects as $arObject):?>
		<tr>
			<td><b><?=$arObject['TITLE']?></b></td>
			<td align="center">
				<a href="<?=$curDir?>class_methods_list.php?id=<?=$classID?>"><?=$arClass['TITLE']?></a>
			</td>
			<td><?=$arObject['DESCRIPTION']?>&nbsp;</td>
			<td align="center"><?if(intval($arObject['ROOM_ID'])>0):?>[<?=$arObject['ROOM_TITLE']?>]<br><?=$arObject['ROOM_DESCRIPTION']?><?else:?>&nbsp;<?endif;?></td>
			<td>
				<a href="<?=$curDir?>class_object_edit.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>" class="btn btn-default btn-sm" title="Редактировать"><i class="glyphicon glyphicon-pencil"></i></a>
				<a href="<?=$curDir?>object_properties_list.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>" class="btn btn-default btn-sm" title="Свойства"><i class="glyphicon glyphicon-th"></i></a>
				<a href="<?=$curDir?>object_methods_list.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>" class="btn btn-default btn-sm" title="Методы"><i class="glyphicon glyphicon-th-list"></i></a>
				<a href="<?=$curPage?>?mode=delete&id=<?=$classID?>&objectID=<?=$arObject['ID']?>" onclick="return confirm('Вы уверены, что хотите удалить объект <?=$arObject['TITLE']?>?')" class="btn btn-default btn-sm" title="Удалить"><i class="glyphicon glyphicon-remove"></i></a>
			</td>
		</tr>
	<?endforeach;?>
<?endif;?>

</tbody></table>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
