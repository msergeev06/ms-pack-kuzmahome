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
if (isset($_REQUEST['redefineMethod']) && intval($_REQUEST['redefineMethod'])>0)
{
	$arMethod = Tables\MethodsTable::getList(
		array(
			'filter' => array('ID'=>intval($_REQUEST['redefineMethod'])),
			'limit' => 1
		)
	);
	if ($arMethod && isset($arMethod[0]))
	{
		$arMethod = $arMethod[0];
	}
	if ($arMethod)
	{
		unset($arMethod['ID']);
		$arMethod['CLASS_ID'] = NULL;
		$arMethod['EXECUTED'] = NULL;
		$arMethod['EXECUTED_PARAMS'] = NULL;
		$arMethod['OBJECT_ID'] = $objectID;
		$arMethod['ID'] = Tables\MethodsTable::add(array("VALUES"=>$arMethod))->getInsertId();
		if ($arMethod['ID'])
		{
			\MSergeev\Core\Lib\Buffer::setRefresh(
				$curDir.'object_method_edit.php?show=success&classID='.$classID.'&objectID='.$objectID.'&id='.$arMethod['ID']);
		}
	}
}
$arClassMethods = Tables\MethodsTable::getList(
	array(
		'select' => array('ID','TITLE','DESCRIPTION'),
		'filter' => array('CLASS_ID'=>$classID),
		'order' => array('TITLE'=>'ASC')
	)
);
$arObjectMethods = Tables\MethodsTable::getList(
	array(
		'select' => array('ID','TITLE','DESCRIPTION'),
		'filter' => array('OBJECT_ID'=>$objectID),
		'order' => array('TITLE'=>'ASC')
	)
);
if ($arObjectMethods){
	$arTemp = $arObjectMethods;
	$arObjectMethods = array();
	foreach ($arTemp as $temp)
	{
		$arObjectMethods[$temp['TITLE']] = $temp;
	}
}
else
{
	$arObjectMethods = array();
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
	<li><a href="<?=$curDir?>object_properties_list.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>">Свойства</a></li>
	<li class="active"><a href="<?=$curPage?>?classID=<?=$classID?>&id=<?=$objectID?>">Методы</a></li>
</ul>
<?if($bShowError):?>
	<span class="text-danger"><?=$textError?></span><br><br>
<?endif;?>
<?if($bShowSuccess):?>
	<span class="text-success">Данные сохранены</span><br><br>
<?endif;?>
<table border="0" class="table table-bordered"><tbody>
	<?if($arClassMethods):?>
		<?foreach($arClassMethods as $arClassMethod):?>
			<tr>
				<td><?=$arClass['TITLE']?>-&gt; <b><?=$arClassMethod['TITLE']?></b>
					<?if(isset($arObjectMethods[$arClassMethod['TITLE']])):?>
						<span style="color:red;">*</span>
					<?endif;?>
					<?if(isset($arObjectMethods[$arClassMethod['TITLE']])
						&& strlen($arObjectMethods[$arClassMethod['TITLE']]['DESCRIPTION'])>0):?>
						<br><i><?=$arObjectMethods[$arClassMethod['TITLE']]['DESCRIPTION']?></i>
					<?elseif(!isset($arObjectMethods[$arClassMethod['TITLE']])
						&& strlen($arClassMethod['DESCRIPTION'])>0):?>
						<br><i><?=$arClassMethod['DESCRIPTION']?></i>
					<?endif;?>
				</td>
				<?
				if(isset($arObjectMethods[$arClassMethod['TITLE']])){
					$url = $curDir.'object_method_edit.php?classID='.$classID.'&objectID='.$objectID
						.'&id='.$arObjectMethods[$arClassMethod['TITLE']]['ID'];
				}
				else
				{
					$url = $curPage.'?classID='.$classID.'&id='.$objectID
						.'&redefineMethod='.$arClassMethod['ID'];
				}
				?>
				<td><a href="<?=$url?>" class="btn btn-default btn-sm">Настроить</a></td>
			</tr>
			<?/*
			if(isset($arObjectMethods[$arClassMethod['TITLE']]))
			{
				unset($arObjectMethods[$arClassMethod['TITLE']]);
			}
			*/?>
		<?endforeach;?>
	<?endif;?>
	<?/*if(!empty($arObjectMethods)):?>
		<?foreach($arObjectMethods as $methodTitle=>$arObjectMethod):?>
			<tr>
				<td><?=$arObject['TITLE']?>-&gt; <b><?=$methodTitle?></b></td>
				<td><a href="<?=$curDir?>object_method_edit.php?classID=<?=$classID?>&objectID=<?=$objectID?>&id=<?=$arObjectMethod['ID']?>" class="btn btn-default btn-sm">Настроить</a></td>
			</tr>
		<?endforeach;?>
	<?endif;*/?>

</tbody></table>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
