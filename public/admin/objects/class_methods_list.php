<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Методы класса');
use MSergeev\Packages\Kuzmahome\Tables;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$classID = 0;
$bShowSuccess = false;
if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='delete'
	&& isset($_REQUEST['methodID']) && $_REQUEST['methodID']>0
)
{
	Tables\MethodsTable::delete($_REQUEST['methodID'],true);
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
	$arMethods = Tables\MethodsTable::getList(
		array(
			'select' => array('ID','TITLE','DESCRIPTION'),
			'filter' => array('CLASS_ID'=>$classID),
			'order' => array('TITLE'=>'ASC')
		)
	);
	if (!$arMethods)
	{
		$bShowMethods = false;
	}
	else
	{
		$bShowMethods = true;
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
	<li class="active"><a href="<?=$curPage?>?id=<?=$classID?>">Методы</a></li>
	<li><a href="<?=$curDir?>class_objects_list.php?id=<?=$classID?>">Объекты</a></li>
</ul><br>
<?if($bShowSuccess):?>
	<span class="text-success">Данные сохранены</span><br><br>
<?endif;?>
<a href="<?=$curDir?>class_method_add.php?id=<?=$classID?>" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i> Добавить новый метод</a><br>
<table class="table table-bordered">
	<tbody><tr>
		<td>
			<b>Название</b>
		</td>
		<td>
			<b>Описание</b>
		</td>
		<td>&nbsp;</td>
	</tr>
	<?if($bShowMethods):?>
		<?foreach($arMethods as $arMethod):?>
			<tr>
				<td><a href="<?=$curDir?>class_method_edit.php?classID=<?=$classID?>&id=<?=$arMethod['ID']?>" title="Редактировать"><?=$arMethod['TITLE']?></a></td>
				<td><?=$arMethod['DESCRIPTION']?>&nbsp;</td>
				<td>
					<a href="<?=$curDir?>class_method_edit.php?classID=<?=$classID?>&id=<?=$arMethod['ID']?>" title="Редактировать" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-pencil"></i></a>
					<a href="<?=$curPage?>?mode=delete&id=<?=$classID?>&methodID=<?=$arMethod['ID']?>" onclick="return confirm('Вы уверены, что хотите удалить метод <?=$arMethod['TITLE']?>?')" title="Удалить" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-remove"></i></a>
				</td>
			</tr>
		<?endforeach;?>
	<?endif;?>
</tbody></table>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
