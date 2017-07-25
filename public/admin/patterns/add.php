<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Шаблоны поведения');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Patterns\Tables as PTables;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$pathTools = MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_TOOLS_ROOT');
\MSergeev\Core\Lib\Loader::IncludePackage('patterns');
$arContextsPatterns = PTables\PatternsTable::getList(
	array(
		'select' => array('ID','TITLE'),
		'filter' => array('IS_CONTEXT'=>true),
		'order' => array('PRIORITY'=>'DESC')
	)
);
$arPattern = array();
if (isset($_POST['PARENT_ID']))
{
	$arPattern['PARENT_ID'] = intval($_POST['PARENT_ID']);
}
else
{
	$arPattern['PARENT_ID'] = 0;
}
if ($arPattern['PARENT_ID']==0)
{
	$arPattern['PARENT_ID']=NULL;
}
if (isset($_POST['TITLE']) && strlen($_POST['TITLE'])>0)
{
	$arPattern['TITLE'] = $_POST['TITLE'];
}
else
{
	$arPattern['TITLE'] = '';
}
if (isset($_POST['PRIORITY']) && intval($_POST['PRIORITY'])>0)
{
	$arPattern['PRIORITY'] = intval($_POST['PRIORITY']);
}
else
{
	$arPattern['PRIORITY'] = 100;
}
if (isset($_POST['IS_GLOBAL']) && $_POST['IS_GLOBAL']=='Y')
{
	$arPattern['IS_GLOBAL'] = true;
}
else
{
	$arPattern['IS_GLOBAL'] = false;
}
if (isset($_POST['IS_CONTEXT']) && $_POST['IS_CONTEXT']=='Y')
{
	$arPattern['IS_CONTEXT'] = true;
}
else
{
	$arPattern['IS_CONTEXT'] = false;
}
if (isset($_POST['SKIP_SYSTEM']))
{
	if ($_POST['SKIP_SYSTEM']=='Y')
	{
		$arPattern['SKIP_SYSTEM'] = true;
	}
	else
	{
		$arPattern['SKIP_SYSTEM'] = false;
	}
}
else
{
	$arPattern['SKIP_SYSTEM'] = true;
}
if (isset($_POST['IS_LAST']))
{
	if ($_POST['IS_LAST']=='Y')
	{
		$arPattern['IS_LAST'] = true;
	}
	else
	{
		$arPattern['IS_LAST'] = false;
	}
}
else
{
	$arPattern['IS_LAST'] = true;
}
if (isset($_POST['action']))
{
	$res = PTables\PatternsTable::add(array("VALUES"=>$arPattern));
	if ($res->getResult())
	{
		$arPattern['ID'] = $res->getInsertId();
		\MSergeev\Core\Lib\Buffer::setRefresh($curDir.'edit.php?id='.$arPattern['ID']);
	}
}
?>
<br>
<ul class="breadcrumb">
	<li><a href="index.php">Начало</a></li>
	<span class="divider">/</span>
	<li class="active">Новая запись</li>
</ul>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" name="frmEdit" id="frmEdit">
	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Доступно в контексте: <a href="http://majordomo.smartliving.ru/Hints/pattern_context?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<select name="PARENT_ID" class="form-control">
				<option value="0"<?if($arPattern['PARENT_ID']==0):?> selected<?endif;?>>--- Выбрать ---</option>
				<?if($arContextsPatterns):?>
					<?foreach($arContextsPatterns as $pattern):?>
						<option value="<?=intval($pattern['ID'])?>"<?if($arPattern['PARENT_ID']==intval($pattern['ID'])):?> selected<?endif;?>>(<?=intval($pattern['ID'])?>)&nbsp;<?=$pattern['TITLE']?></option>
					<?endforeach;?>
				<?endif;?>
			</select>
		</div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Название:<span style="color:red">*</span> <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="TITLE" value="<?=$arPattern['TITLE']?>" placeholder="Название шаблона / регулярное выражение"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Приоритет: <a href="http://majordomo.smartliving.ru/Hints/priority?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><input type="number" min="0" step="1" class="form-control" name="PRIORITY" value="<?=$arPattern['PRIORITY']?>"></div>
	</div>
	<div class="form-group">
		<label class="col-lg-4 control-label" for="inputTitle">&nbsp;</label>
		<div class="col-lg-5">
			<div class="checkbox">
				<label>
					<input type="checkbox" value="Y" name="IS_GLOBAL"<?if($arPattern['IS_GLOBAL']):?> checked<?endif;?>>Является глобальным контекстом
				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-4 control-label" for="inputTitle">&nbsp;</label>
		<div class="col-lg-5">
			<div class="checkbox">
				<label>
					<input type="checkbox" value="Y" name="IS_CONTEXT"<?if($arPattern['IS_CONTEXT']):?> checked<?endif;?>>Является контекстом
				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-4 control-label" for="inputTitle">&nbsp;</label>
		<div class="col-lg-5">
			<div class="checkbox">
				<label>
					<input type="checkbox" value="Y" name="SKIP_SYSTEM"<?if($arPattern['SKIP_SYSTEM']):?> checked<?endif;?>>Пропускать сообщения системы
				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-4 control-label" for="inputTitle">&nbsp;</label>
		<div class="col-lg-5">
			<div class="checkbox">
				<label>
					<input type="checkbox" value="Y" name="IS_LAST"<?if($arPattern['IS_LAST']):?> checked<?endif;?>>Не проверять другие шаблоны при совпадении
				</label>
			</div>
		</div>
	</div>

	<input type="hidden" name="action" value="1">
	<div class="form-group">
		<label class="col-lg-4 control-label" for="inputTitle">&nbsp;</label>
		<div class="col-lg-5">
			<input type="submit" name="subm" value="Добавить" class="btn btn-default btn-primary">
			&nbsp;
			<a href="index.php" class="btn btn-default ">Отмена</a>
		</div>
	</div>
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
