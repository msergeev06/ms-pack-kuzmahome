<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Шаблоны поведения');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Patterns\Tables as PTables;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$pathTools = MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_TOOLS_ROOT');
\MSergeev\Core\Lib\Loader::IncludePackage('patterns');
\MSergeev\Core\Lib\Plugins::includeCodeMirror();
$arContextsPatterns = PTables\PatternsTable::getList(
	array(
		'select' => array('ID'=>'VALUE','TITLE'=>'NAME'),
		'filter' => array('IS_CONTEXT'=>true),
		'order' => array('PRIORITY'=>'DESC')
	)
);
if (!$arContextsPatterns)
{
	$arContextsPatterns = array();
}
$arScripts = Tables\ScriptsTable::getList(
	array(
		'select' => array('ID'=>'VALUE','TITLE'=>'NAME'),
		'order' => array('TITLE'=>'ASC')
	)
);
if (!$arScripts)
{
	$arScripts = array();
}
$arPattern = PTables\PatternsTable::getById(intval($_REQUEST['id']));
if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='clone')
{
	unset($arPattern['ID']);
	$arPattern['TITLE'].=' (copy)';
	$newID = PTables\PatternsTable::add(array("VALUES"=>$arPattern))->getInsertId();
	\MSergeev\Core\Lib\Buffer::setRefresh($curDir.'edit.php?id='.$newID);
}
if (isset($_POST['PARENT_ID']) && intval($_POST['PARENT_ID'])>0)
{
	$arPattern['PARENT_ID'] = intval($_POST['PARENT_ID']);
}
elseif (isset($_POST['action']))
{
	$arPattern['PARENT_ID'] = NULL;
}
if (isset($_POST['TITLE']))
{
	$arPattern['TITLE'] = $_POST['TITLE'];
}
if (isset($_POST['PRIORITY']) && intval($_POST['PRIORITY'])>0)
{
	$arPattern['PRIORITY'] = intval($_POST['PRIORITY']);
}
if (isset($_POST['PATTERN']) && strlen($_POST['PATTERN'])>0)
{
	$arPattern['PATTERN'] = $_POST['PATTERN'];
}
elseif (isset($_POST['action']))
{
	$arPattern['PATTERN'] = NULL;
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
if (isset($_POST['TIME_LIMIT']) && intval($_POST['TIME_LIMIT'])>=0)
{
	$arPattern['TIME_LIMIT'] = intval($_POST['TIME_LIMIT']);
}
if (isset($_POST['SCRIPT_ID']) && intval($_POST['SCRIPT_ID'])>0)
{
	$arPattern['SCRIPT_ID'] = intval($_POST['SCRIPT_ID']);
}
if (isset($_POST['SCRIPT']) && strlen($_POST['SCRIPT'])>0)
{
	$arPattern['SCRIPT'] = $_POST['SCRIPT'];
}
elseif (isset($_POST['action']))
{
	$arPattern['SCRIPT'] = NULL;
}
if (isset($_POST['IS_LAST']) && $_POST['IS_LAST']=='Y')
{
	$arPattern['IS_LAST'] = true;
}
elseif (isset($_POST['action']))
{
	$arPattern['IS_LAST'] = false;
}
if (isset($_POST['SCRIPT_EXIT']) && strlen($_POST['SCRIPT_EXIT'])>0)
{
	$arPattern['SCRIPT_EXIT'] = $_POST['SCRIPT_EXIT'];
}
elseif (isset($_POST['action']))
{
	$arPattern['SCRIPT_EXIT'] = NULL;
}
if (isset($_POST['SKIP_SYSTEM']) && $_POST['SKIP_SYSTEM']=='Y')
{
	$arPattern['SKIP_SYSTEM'] = true;
}
elseif (isset($_POST['action']))
{
	$arPattern['SKIP_SYSTEM'] = false;
}
if (isset($_POST['DESCRIPTION']) && strlen($_POST['DESCRIPTION'])>0)
{
	$arPattern['DESCRIPTION'] = $_POST['DESCRIPTION'];
}
elseif (isset($_POST['action']))
{
	$arPattern['DESCRIPTION'] = NULL;
}
//msDebug($arPattern);
?>
<br>
<ul class="breadcrumb">
	<li><a href="index.php">Начало</a></li>
	<span class="divider">/</span>
	<li class="active">Редактирование записи</li>
</ul>
<?
if (isset($_POST['id']) && isset($_POST['action']))
{
	unset($arPattern['ID']);
	$res = PTables\PatternsTable::update(intval($_POST['id']),array("VALUES"=>$arPattern));
	if ($res->getResult())
	{
		?><br><span class="text-success">Данные сохранены</span><br><?
	}
	$arPattern['ID'] = intval($_POST['id']);
}
?>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" name="frmEdit" id="frmEdit">
	<div class="form-group">
		<label class="col-lg-4 control-label">ID: <a href="http://majordomo.smartliving.ru/Hints/id?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<input type="text" value="<?=$arPattern['ID']?>" disabled="" class="form-control">
		</div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Доступно в контексте: <a href="http://majordomo.smartliving.ru/Hints/pattern_context?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<?=SelectBox('PARENT_ID',$arContextsPatterns,'--- Выбрать ---','null','class="form-control"')?>
		</div>
	</div>


	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Название:<span style="color:red">*</span> <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="TITLE" value="<?=$arPattern['TITLE']?>"></div>
	</div>


	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Приоритет: <a href="http://majordomo.smartliving.ru/Hints/priority?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><input type="text" class="form-control" name="PRIORITY" value="<?=$arPattern['PRIORITY']?>"></div>
	</div>
	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Шаблон: <a href="http://majordomo.smartliving.ru/Hints/pattern_data?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><textarea name="PATTERN" class="form-control " rows="3" cols="100" placeholder="Регулярное выражение"><?=$arPattern['PATTERN']?></textarea></div>
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


	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Лимит времени контекста, секунд:<span style="color:red">*</span> <a href="http://majordomo.smartliving.ru/Hints/pattern_timelimit?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="TIME_LIMIT" value="<?=$arPattern['TIME_LIMIT']?>"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Выполнить при совпадении <a href="http://majordomo.smartliving.ru/Hints/code?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a>:</label>
		<div class="col-lg-5">
			<input type="radio" name="run_type" value="script" onclick="$('#code_option').hide();"> Сценарий:
			<?=SelectBox('SCRIPT_ID',$arScripts,'--- Выбрать ---','null','class="form-control"')?>
			<br>
			<input type="radio" name="run_type" value="code" checked="" onclick="$('#code_option').show();"> Код
			<div id="code_option">
				<?\MSergeev\Packages\Kuzmahome\Lib\Objects::showCodemirrorScript('script')?>
				<div id="script_area">
					<textarea name="SCRIPT" id="script" rows="20" cols="100" class="field span8" style="display: none;"><?=$arPattern['SCRIPT']?></textarea>
				</div>
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

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Выполнить при истечении <a href="http://majordomo.smartliving.ru/Hints/code?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a>:</label>
		<div class="col-lg-5">
			<?\MSergeev\Packages\Kuzmahome\Lib\Objects::showCodemirrorScript('script_exit')?>
			<div id="script_area">
				<textarea name="SCRIPT_EXIT" id="script_exit" rows="20" cols="100" class="field span8" style="display: none;"><?=$arPattern['SCRIPT_EXIT']?></textarea>
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

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Описание: <a href="http://majordomo.smartliving.ru/Hints/pattern_data?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><textarea name="DESCRIPTION" class="form-control " rows="3" cols="100" placeholder="Описание действий шаблона для функции help"><?=$arPattern['DESCRIPTION']?></textarea></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Контроль доступа: <a href="http://majordomo.smartliving.ru/Hints/access_control?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<a class="btn btn-default " href="#" target="_blank">установить</a>
		</div>
	</div>

	<input type="hidden" name="id" value="<?=$arPattern['ID']?>">
	<input type="hidden" name="action" value="1">
	<div class="form-group">
		<label class="col-lg-4 control-label" for="inputTitle">&nbsp;</label>
		<div class="col-lg-5">
			<input type="submit" name="subm" value="Сохранить" class="btn btn-default btn-primary">
			&nbsp;
			<a href="index.php" class="btn btn-default ">Отмена</a>
			&nbsp;
			<a class="btn btn-default btn-primary" href="?mode=clone&id=<?=$arPattern['ID']?>" onclick="return confirm('Вы уверены? Пожалуйста, подтвердите операцию.')">Создать копию (клонировать)</a>
		</div>
	</div>
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
