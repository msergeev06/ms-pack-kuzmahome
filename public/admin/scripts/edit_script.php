<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Скрипты - Редактирование');
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
	$scriptID = null;
	if (isset($_POST['id']) && intval($_POST['id'])>0)
	{
		$scriptID = intval($_POST['id']);
		if (isset($_POST['title']) && strlen($_POST['title'])>0)
		{
			$arCheck = Tables\ScriptsTable::getOne(
				array(
					'select' => array('ID'),
					'filter' => array('TITLE'=>$_POST['title'])
				)
			);
			if (!$arCheck || $arCheck['ID']==$scriptID)
			{
				$arScript['TITLE'] = $_POST['title'];
			}
			else
			{
				$arError['TITLE_EXISTS'] = 'Уже существует сценарий с данным названием';
			}
		}
		else
		{
			$arError['TITLE_NO_EXISTS'] = 'Не указан заголовок сценария';
		}
	}
	else
	{
		$arError['WRONG_ID'] = 'Неверный ID сценария';
	}
	if (isset($_POST['run_periodically']) && intval($_POST['run_periodically'])==1)
	{
		$arScript['RUN_PERIODICALLY'] = true;
		if (isset($_POST['run_hours']))
		{
			$run_hours = $_POST['run_hours'];
		}
		else
		{
			$run_hours = '00';
		}
		if (isset($_POST['run_minutes']))
		{
			$run_minutes = $_POST['run_minutes'];
		}
		else
		{
			$run_minutes = '00';
		}
		$arScript['RUN_TIME'] = $run_hours.':'.$run_minutes;
		if (isset($_POST['run_days']) && !empty($_POST['run_days']))
		{
			$arScript['RUN_DAYS'] = implode(',',$_POST['run_days']);
		}
		else
		{
			$arScript['RUN_DAYS'] = NULL;
		}
	}
	else
	{
		$arScript['RUN_PERIODICALLY'] = false;
		$arScript['RUN_TIME'] = NULL;
		$arScript['RUN_DAYS'] = NULL;
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
	if (isset($_POST['code']) && strlen($_POST['code'])>0)
	{
		$arScript['CODE'] = $_POST['code'];
	}
	else
	{
		$arScript['CODE'] = NULL;
	}

	if (!is_null($scriptID) && isset($arScript['TITLE']))
	{
		//Обновляем сценарий
		Tables\ScriptsTable::update($scriptID,array("VALUES"=>$arScript));
		//Если установлена галка "Запускать после сохранения" - запускаем сценарий
		if (isset($_POST['edit_run']) && intval($_POST['edit_run'])==1)
		{
			Lib\Scripts::runScript($scriptID);
		}
	}
	//msDebug($arScript);
}
if (isset($_REQUEST['id']) && intval($_REQUEST['id'])>0)
{
	$arScript = Tables\ScriptsTable::getOne(
		array(
			'select' => array('ID','TITLE','DESCRIPTION','CATEGORY_ID','CODE','RUN_PERIODICALLY','RUN_DAYS','RUN_TIME'),
			'filter' => array('ID'=>intval($_REQUEST['id']))
		)
	);
	//Если нужно создать копию сценария
	if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='clone')
	{
		//$scriptID = $arScript['ID'];
		unset($arScript['ID']);
		$arScript['TITLE'].='_copy';
		$arScript['ID'] = Tables\ScriptsTable::add(array("VALUES"=>$arScript))->getInsertId();
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
	<li class="active">Редактирование записи</li>
</ul>
<br>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" id="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Название:<span style="color:red">*</span> <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="title" value="<?=$arScript['TITLE']?>" required="true"></div>
	</div>

	<div class="form-group ">
		<div class="col-lg-5 col-lg-offset-2">
			<label><input type="checkbox" name="run_periodically" value="1" onclick="$('#periocidally').toggle();">
				Выполнять периодически <a href="http://majordomo.smartliving.ru/Hints/script_runperiodically?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
			<div id="periocidally" style="<?if(!$arScript['RUN_PERIODICALLY']):?>display:none<?else:?>display:block<?endif;?>">
				<?
				$hour = $min = '00';
				if ($arScript['RUN_PERIODICALLY'])
				{
					if (!is_null($arScript['RUN_TIME']) && strlen($arScript['RUN_TIME'])>0)
					{
						list($hour,$min) = explode(':',$arScript['RUN_TIME']);
					}
				}
				?>
				Время запуска:
				<select name="run_hours" class="span2">
					<?for($i=0;$i<=23;$i++):?>
						<?
						if ($i>=0 && $i<=9) $n='0'.$i;
						else $n = $i;
						?>
						<option value="<?=$n?>"<?if($i==intval($hour)):?> selected<?endif;?>><?=$n?></option>
					<?endfor;?>
				</select>:<select name="run_minutes" class="span2">
					<?for($i=0;$i<=59;$i++):?>
						<?
						if ($i>=0 && $i<=9) $n='0'.$i;
						else $n = $i;
						?>
						<option value="<?=$n?>"<?if($i==intval($min)):?> selected<?endif;?>><?=$n?></option>
					<?endfor;?>
				</select>
				<br>
				<?
				$arRunDaysChecked = array();
				if ($arScript['RUN_PERIODICALLY'])
				{
					if (!is_null($arScript['RUN_DAYS']) && strlen($arScript['RUN_DAYS'])>0)
					{
						$arRunDays = explode(',',$arScript['RUN_DAYS']);
						if (!empty($arRunDays))
						{
							foreach($arRunDays as $run_day)
							{
								$arRunDaysChecked[$run_day] = true;
							}
						}
					}
				}
				?>
				Дни недели:

				<br><label><input type="checkbox" name="run_days[]" value="1"<?if(isset($arRunDaysChecked[1])):?> checked<?endif;?>> Понедельник</label>

				<br><label><input type="checkbox" name="run_days[]" value="2"<?if(isset($arRunDaysChecked[2])):?> checked<?endif;?>> Вторник</label>

				<br><label><input type="checkbox" name="run_days[]" value="3"<?if(isset($arRunDaysChecked[3])):?> checked<?endif;?>> Среда</label>

				<br><label><input type="checkbox" name="run_days[]" value="4"<?if(isset($arRunDaysChecked[4])):?> checked<?endif;?>> Четверг</label>

				<br><label><input type="checkbox" name="run_days[]" value="5"<?if(isset($arRunDaysChecked[5])):?> checked<?endif;?>> Пятница</label>

				<br><label><input type="checkbox" name="run_days[]" value="6"<?if(isset($arRunDaysChecked[6])):?> checked<?endif;?>> Суббота</label>

				<br><label><input type="checkbox" name="run_days[]" value="0"<?if(isset($arRunDaysChecked[0])):?> checked<?endif;?>> Воскресенье</label>

			</div>

		</div>
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


	<div class="form-group ">
		<label class="col-lg-4 control-label">Код <a href="http://majordomo.smartliving.ru/Hints/code?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<?Lib\Scripts::showCodemirrorScript()?>
			<div id="code_area">
				<textarea name="code" id="code" rows="30" cols="100" class="form-control" style="display: none;"><?=$arScript['CODE']?></textarea>
			</div>
		</div>
	</div>

	<div class="form-group ">
		<div class="col-lg-5 col-lg-offset-2">
			<label>
				<input type="checkbox" name="edit_run" id="chkRun" value="1"> выполнить после сохранения
			</label>
		</div>
	</div>

	<input type="hidden" name="id" value="<?=$arScript['ID']?>">
	<input type="hidden" name="action" value="1">

	<div class="form-group">
		<div class="col-lg-offset-1 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Сохранить">
			&nbsp;
			<a href="index.php" class="btn btn-default">Отмена</a>
			&nbsp;
			<a href="?mode=clone&id=<?=$arScript['ID']?>" class="btn btn-default" onclick="return confirm('Вы уверены? Пожалуйста, подтвердите операцию.')">Создать копию (клонировать)</a>
		</div>
	</div>

	&nbsp;
	<table>
	<tbody><tr><td valign="top">Запуск по ссылке:</td>
		<td valign="top"><a target="_blank" href="<?=\MSergeev\Core\Lib\Config::getConfig('HTTP_HTTPS')?>://<?=\MSergeev\Core\Lib\Config::getConfig('SITE_URL')?>scripts.php?id=<?=$arScript['TITLE']?>" target="_blank"><?=\MSergeev\Core\Lib\Config::getConfig('HTTP_HTTPS')?>://<?=\MSergeev\Core\Lib\Config::getConfig('SITE_URL')?>scripts.php?id=<?=$arScript['TITLE']?></a></td>
	</tr>
	<?/*
	<tr>
		<td valign="top">Через командную строку:</td>
		<td valign="top">/var/www\obj.bat script:SayWhoCall</td>
	</tr>*/?>
	</tbody>
	</table>
	&nbsp;
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
