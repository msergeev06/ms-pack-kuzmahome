<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Скрипты - Добавление');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$pathTools = MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_TOOLS_ROOT');
\MSergeev\Core\Lib\Plugins::includeCodeMirror();
if (!$view = $USER->getUserCookie('admin_scripts_page_view'))
{
	$view = 'list';
	$USER->setUserCookie('admin_scripts_page_view','list');
}
if (isset($_REQUEST['view']) && ($_REQUEST['view']=='list' || $_REQUEST['view']=='category'))
{
	$view = $_REQUEST['view'];
	$USER->setUserCookie('admin_scripts_page_view',$view);
}
if (isset($_POST['action']) && intval($_POST['action'])==1)
{
	$arScript = array();
	if (isset($_POST['title']) && strlen($_POST['title'])>0)
	{
		$arCheck = Tables\ScriptsTable::getOne(
			array(
				'select' => array('ID'),
				'filter' => array('TITLE'=>$_POST['title'])
			)
		);
		if (!$arCheck)
		{
			$arScript['TITLE'] = $_POST['title'];
		}
		else
		{
			$arError['TITLE_EXISTS'] = 'Уже существует сценарий с данным названием';
			?><p class="text-danger">Уже существует сценарий с данным названием</p><?
		}
	}
	else
	{
		$arError['TITLE_NO_EXISTS'] = 'Не указан заголовок сценария';
		?><p class="text-danger">Не указан заголовок сценария</p><?
	}
	if (isset($_POST['category_id']) && intval($_POST['category_id'])>0)
	{
		$arScript['CATEGORY_ID'] = intval($_POST['category_id']);
	}
	else
	{
		$arScript['CATEGORY_ID'] = NULL;
	}
	if (isset($_POST['description']) && strlen($_POST['description'])>0)
	{
		$arScript['DESCRIPTION'] = $_POST['description'];
	}
	else
	{
		$arScript['DESCRIPTION'] = NULL;
	}

	if (isset($arScript['TITLE']))
	{
		$arScript['CODE'] = '';
		$scriptID = Tables\ScriptsTable::add(array("VALUES"=>$arScript))->getInsertId();
		\MSergeev\Core\Lib\Buffer::setRefresh($curDir.'edit_script.php?id='.$scriptID);
	}
	//msDebug($arScript);
}
$arCategoryList = Tables\ScriptsCategoriesTable::getList(
	array(
		'select' => array('ID','TITLE'),
		'order' => array('TITLE'=>'ASC')
	)
);
?>
<ul class="nav nav-tabs">
	<li<?=(($view=='list')?' class="active"':'')?>><a href="index.php?view=list">Сценарии</a></li>
	<li<?=(($view=='category')?' class="active"':'')?>><a href="index.php?view=category">Категории</a></li>
</ul>
<br>
<ul class="breadcrumb">
	<li><a href="<?=$curDir?>">Начало</a></li>
	<span class="divider">/</span>
	<li class="active">Добавление сценария</li>
</ul>
<br>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" id="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Название:<span style="color:red">*</span> <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="title" value="" required="true"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label">Категория <a href="http://majordomo.smartliving.ru/Hints/script_category?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<select name="category_id" class="form-control">
				<option value="0"<?if(intval($arScript['CATEGORY_ID'])==0):?> selected<?endif;?>></option>
				<?if($arCategoryList):?>
					<?foreach($arCategoryList as $arCategory):?>
						<option value="<?=$arCategory['ID']?>"<?if(intval($arScript['CATEGORY_ID'])==$arCategory['ID']):?> selected<?endif;?>><?=$arCategory['TITLE']?></option>
					<?endforeach;?>
				<?endif;?>
			</select>

		</div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label">Описание <a href="http://majordomo.smartliving.ru/Hints/description?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<textarea name="description" id="description" rows="3" cols="100" class="form-control"><?=$arScript['DESCRIPTION']?></textarea>
		</div>
	</div>

	<input type="hidden" name="action" value="1">

	<div class="form-group">
		<div class="col-lg-offset-1 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Добавить">
			&nbsp;
			<a href="index.php" class="btn btn-default">Отмена</a>
		</div>
	</div>

	&nbsp;
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
