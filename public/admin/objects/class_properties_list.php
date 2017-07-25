<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Свойства класса');
use MSergeev\Packages\Kuzmahome\Tables;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$classID = 0;
$bShowSuccess = false;
if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='delete'
	&& isset($_REQUEST['propID']) && $_REQUEST['propID']>0
)
{
	Tables\PropertiesTable::delete($_REQUEST['propID'],true);
}
if (isset($_REQUEST['show']) && $_REQUEST['show']=='success')
{
	$bShowSuccess = true;
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
	$arProperties = Tables\PropertiesTable::getList(
		array(
			'select' => array('ID','TITLE','DESCRIPTION'),
			'filter' => array('CLASS_ID'=>$classID),
			'order' => array('TITLE'=>'ASC')
		)
	);
	if (!$arProperties)
	{
		$bShowProperties = false;
	}
	else
	{
		$bShowProperties = true;
	}
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
<?if($bShowSuccess):?>
	<span class="text-success">Данные сохранены</span><br><br>
<?endif;?>
<a href="<?=$curDir?>property_add.php?classID=<?=$classID?>" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i> Добавить новое свойство</a><br>
<table class="table">
	<tbody><tr>
		<td>
			<b>Название</b>
		</td>
		<td>
			<b>Описание</b>
		</td>
		<td>&nbsp;</td>
	</tr>
<?if($bShowProperties):?>
	<?foreach ($arProperties as $arProp):?>
	<tr>
		<td><b><?=$arProp['TITLE']?></b></td>
		<td><?=$arProp['DESCRIPTION']?>&nbsp;</td>
		<td width="200">
			<a href="<?=$curDir?>property_edit.php?classID=<?=$classID?>&id=<?=$arProp['ID']?>" title="Редактировать" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-pencil"></i></a>
			<a href="<?=$curPage?>?mode=delete&id=<?=$classID?>&propID=<?=$arProp['ID']?>" title="Удалить" class="btn btn-default btn-sm" onclick="return confirm('Вы уверены, что хотите удалить свойство <?=$arProp['TITLE']?>?')"><i class="glyphicon glyphicon-remove"></i></a>
		</td>
	</tr>
	<?endforeach;?>
<?endif;?>
	</tbody></table>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
