<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Редактирование объекта класса');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
\MSergeev\Core\Lib\Plugins::includeCodeMirror();
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
if (isset($_REQUEST['objectID']))
{
	$objectID = intval($_REQUEST['objectID']);
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
$methodID = 0;
if (isset($_REQUEST['id']))
{
	$methodID = intval($_REQUEST['id']);
}
if ($methodID==0)
{
	?><span class="text-danger">ID метода не может равняться 0 (нулю)</span><?
	die();
}
else
{
	$arMethod = Tables\MethodsTable::getList(
		array(
			'select' => array('ID','TITLE','DESCRIPTION','CODE','CALL_PARENT','SCRIPT_ID'),
			'filter' => array('ID' => $methodID),
			'limit' => 1
		)
	);
	if ($arMethod && isset($arMethod[0]))
	{
		$arMethod = $arMethod[0];
	}
	if (!$arMethod)
	{
		?><span class="text-danger">Ошибка загрузки данных метода</span><?
		die();
	}
}
if (isset($_POST['action']))
{
	$arUpdate = $arMethod;
	unset($arUpdate['ID']);
	unset($arUpdate['TITLE']);
	$arMethod['DESCRIPTION']    = $arUpdate['DESCRIPTION']  = $_POST['description'];
	$arMethod['CODE']           = $arUpdate['CODE']         = $_POST['code'];
	$arMethod['CALL_PARENT']    = $arUpdate['CALL_PARENT']  = intval($_POST['call_parent']);
	$arMethod['SCRIPT_ID']      = $arUpdate['SCRIPT_ID']    = intval($_POST['script_id']);
	Tables\MethodsTable::update($methodID,array("VALUES"=>$arUpdate));
}
$arScripts = Tables\ScriptsTable::getList(
	array(
		'select' => array('ID','TITLE'),
		'order' => array('TITLE'=>'ASC')
	)
);
$description = $arMethod['description'];
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
	<li><a href="<?=$curDir?>class_methods_list.php?id=<?=$classID?>">Методы</a></li>
	<li class="active"><a href="<?=$curDir?>class_objects_list.php?id=<?=$classID?>">Объекты</a></li>
</ul><br>
<ol class="breadcrumb">
	<li class="active">Объект: <?=$arObject['TITLE']?></li>
</ol>
<ul class="nav nav-tabs">
	<li><a href="<?=$curDir?>class_object_edit.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>">Детали</a></li>
	<li><a href="<?=$curDir?>object_properties_list.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>">Свойства</a></li>
	<li class="active"><a href="<?=$curDir?>object_methods_list.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>">Методы</a></li>
</ul>
<?if($bShowError):?>
	<span class="text-danger"><?=$textError?></span><br><br>
<?endif;?>
<?if($bShowSuccess):?>
	<span class="text-success">Данные сохранены</span><br><br>
<?endif;?>
<h4><?=$arMethod['TITLE']?></h4>
<form action="" method="post" name="frmEdit" id="frmEdit">
	<table border="0"><tbody>
		<tr>
			<td>
				Описание метода:<br>
				<textarea name="description" id="description" rows="3" cols="100" class="form-control"><?=$description?></textarea>
			</td>
		</tr>
		<tr>
			<td valign="top">
				Выполнить: <a href="http://majordomo.smartliving.ru/Hints/code?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a><br>
				<input type="radio" name="run_type" value="script" onclick="$('#code_option').hide();"> Сценарий:
				<select name="script_id">
					<option value="0"></option>
					<?if($arScripts):?>
						<?foreach($arScripts as $arScript):?>
							<option value="<?=$arScript['ID']?>"<?if($arMethod['SCRIPT_ID']==$arScript['ID']):?> selected<?endif;?>><?=$arScript['TITLE']?></option>
						<?endforeach;?>
					<?endif;?>
				</select>

				<br><input type="radio" name="run_type" value="code" checked="" onclick="$('#code_option').show();"> Код

				<div id="code_option">
					<?Lib\Objects::showCodemirrorScript()?>
					<div id="code_area">
						<textarea name="code" id="code" rows="20" cols="100" class="field span10" style="display: none;"><?=$arMethod['CODE']?></textarea>
					</div>

				</div>
			</td>
		</tr>
		<tr>
			<td valign="top">&nbsp;<br>
				Вызывать родительский метод:
				<a href="http://majordomo.smartliving.ru/Hints/parent_method?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a>
				<br>
				<label><input type="radio" name="call_parent" value="1"<?if($arMethod['CALL_PARENT']==1):?> checked<?endif;?>> перед выполнением кода</label>
				<label><input type="radio" name="call_parent" value="2"<?if($arMethod['CALL_PARENT']==2):?> checked<?endif;?>> после выполнения кода</label>
				<label><input type="radio" name="call_parent" value="0"<?if($arMethod['CALL_PARENT']==0):?> checked<?endif;?>> никогда</label>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<input type="submit" name="ok" value="Обновить" class="btn btn-primary">
				&nbsp;
				<a href="<?=$curDir?>object_methods_list.php?classID=<?=$classID?>&id=<?=$arObject['ID']?>" class="btn btn-default">Отмена</a>
				&nbsp;&nbsp;&nbsp;
				<a href="<?=$curPage?>?act=delete&classID=<?=$classID?>&objectID=<?=$objectID?>&id=<?=$methodID?>" onclick="return confirm('Вы уверены, что хотите удалить метод <?=$arMethod['TITLE']?> объекта <?=$arObject['TITLE']?>?')" class="btn btn-default">Удалить</a>
			</td>
		</tr>
	</tbody></table>

	<input type="hidden" name="id" value="<?=$methodID?>">
	<input type="hidden" name="classID" value="<?=$classID?>">
	<input type="hidden" name="objectID" value="<?=$objectID?>">
	<input type="hidden" name="code_code_type" value="0">
	<input type="hidden" name="action" value="1">
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
