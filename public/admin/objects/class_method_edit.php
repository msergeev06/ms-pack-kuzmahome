<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Редактирование метода класса');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
\MSergeev\Core\Lib\Plugins::includeCodeMirror();
$classID = 0;
$methodID = 0;
$bShowError = false;
$textError = '';
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
if (isset($_REQUEST['id']))
{
	$methodID = intval($_REQUEST['id']);
}
if ($methodID==0)
{
	?><span class="text-danger">ID метода класса не может равняться 0 (нулю)</span><?
	die();
}
else
{
	$arMethod = Tables\MethodsTable::getList(
		array(
			'select' => array('ID','TITLE','DESCRIPTION','CODE','SCRIPT_ID'),
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
		?><span class="text-danger">Ошибка загрузки данных метода класса</span><?
		die();
	}
}
$arScripts = Tables\ScriptsTable::getList(
	array(
		'select' => array('ID','TITLE'),
		'order' => array('TITLE'=>'ASC')
	)
);
if (isset($_POST['action']))
{
	if ($_POST['title'] == $arMethod['TITLE'])
	{
		Tables\MethodsTable::update(
			$methodID,
			array("VALUES"=>array(
				'TITLE' => $_POST['title'],
				'DESCRIPTION' => $_POST['description'],
				'CODE' => $_POST['code'],
				'SCRIPT_ID' => intval($_POST['script_id'])
			))
		);
		$bShowSuccess = true;
	}
	else
	{
		$arCheck = Tables\MethodsTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array(
					'TITLE'=>$_POST['title'],
					'CLASS_ID' => $classID
				),
				'limit' => 1
			)
		);
		if ($arCheck && isset($arCheck[0]))
		{
			$arCheck = $arCheck[0];
		}
		if ($arCheck)
		{
			$bShowError = true;
			$bShowSuccess = false;
			$textError .= 'Невозможно изменить имя метода, так как метод с данным именем уже существует<br>';
		}
		else
		{
			Tables\MethodsTable::update(
				$methodID,
				array("VALUES"=>array(
					'TITLE' => $_POST['title'],
					'DESCRIPTION' => $_POST['description'],
					'CODE' => $_POST['code'],
					'SCRIPT_ID' => intval($_POST['script_id'])
				))
			);
			$bShowSuccess = true;
		}
	}
}
$title = $arMethod['TITLE'];
if (isset($_POST['title']))
{
	$title = $_POST['title'];
}
$description = $arMethod['DESCRIPTION'];
if (isset($_POST['description']))
{
	$description = $_POST['description'];
}
$code = $arMethod['CODE'];
if (isset($_POST['code']))
{
	$code = $_POST['code'];
}
$script_id = $arMethod['SCRIPT_ID'];
if (isset($_POST['script_id']))
{
	$script_id = intval($_POST['script_id']);
}
?>
<ol class="breadcrumb">
	<li><a href="<?=$curDir?>">Начало</a></li>
	<li class="active"><?=$arClass['TITLE']?></li>
</ol>
<ul class="nav nav-tabs">
	<li><a href="<?=$curDir?>class_edit.php?id=<?=$classID?>">Основное</a></li>
	<li><a href="<?=$curDir?>class_properties_list.php?id=<?=$classID?>">Свойства</a></li>
	<li class="active"><a href="<?=$curDir?>class_methods_list.php?id=<?=$classID?>">Методы</a></li>
	<li><a href="<?=$curDir?>class_objects_list.php?id=<?=$classID?>">Объекты</a></li>
</ul><br>
<?if($bShowError):?>
	<span class="text-danger"><?=$textError?></span>
<?endif;?>
<?if($bShowSuccess):?>
	<span class="text-success">Данные сохранены</span><br><br>
<?endif;?>
<form action="" method="post" enctype="multipart/form-data" name="frmEdit" id="frmEdit" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-4 control-label">Название:<span style="color:red;">*</span> <a href="http://majordomo.smartliving.ru/Hints/title?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="title" value="<?=$title?>" required="true"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label">Описание <a href="http://majordomo.smartliving.ru/Hints/description?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<textarea name="description" id="description" rows="3" cols="100" class="form-control"><?=$description?></textarea>
		</div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label">Код <a href="http://majordomo.smartliving.ru/Hints/code?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<input type="radio" name="run_type" value="script" onclick="$('#code_option').hide();"<?if($script_id>0):?> checked=""<?endif;?>> Сценарий:
			<select name="script_id">
				<option value="0">&nbsp;</option>
				<?if($arScripts):?>
					<?foreach($arScripts as $arScript):?>
						<option value="<?=$arScript['ID']?>" <?if($script_id==$arScript['ID']):?> selected<?endif;?>><?=$arScript['TITLE']?></option>
					<?endforeach;?>
				<?endif;?>
			</select><br>
			<input type="radio" name="run_type" value="code"<?if($script_id==0):?> checked=""<?endif;?> onclick="$('#code_option').show();"> Код
			<div id="code_option" <?if($script_id==0):?>style="display: block;"<?else:?>style="display: none;"<?endif;?>>
				<?Lib\Objects::showCodemirrorScript();?>
				<div id="code_area">
					<textarea name="code" id="code" rows="30" cols="100" class="form-control" style="display: none;"><?=$code?></textarea>
					<?/*
					<div class="CodeMirror cm-s-default"><div style="overflow: hidden; position: relative; width: 3px; height: 0px; top: 4px; left: 44px;"><textarea autocorrect="off" autocapitalize="off" spellcheck="false" style="position: absolute; padding: 0px; width: 1000px; height: 1em; outline: none;" tabindex="0"></textarea></div><div class="CodeMirror-vscrollbar" cm-not-content="true" style="display: block; bottom: 0px;"><div style="min-width: 1px; height: 308px;"></div></div><div class="CodeMirror-hscrollbar" cm-not-content="true"><div style="height: 100%; min-height: 1px; width: 0px;"></div></div><div class="CodeMirror-scrollbar-filler" cm-not-content="true"></div><div class="CodeMirror-gutter-filler" cm-not-content="true"></div><div class="CodeMirror-scroll" tabindex="-1"><div class="CodeMirror-sizer" style="margin-left: 40px; margin-bottom: -17px; border-right-width: 13px; min-height: 308px; min-width: 715px; padding-right: 17px; padding-bottom: 0px;"><div style="position: relative; top: 0px;"><div class="CodeMirror-lines"><div style="position: relative; outline: none;"><div class="CodeMirror-measure">AخA</div><div class="CodeMirror-measure"></div><div style="position: relative; z-index: 1;"></div><div class="CodeMirror-cursors"><div class="CodeMirror-cursor" style="left: 4px; top: 0px; height: 20px;">&nbsp;</div></div><div class="CodeMirror-code"><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">1</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-comment">//Только если задан тип ошибки, воспроизводим сообщение об ошибке</span></span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">2</div><div class="CodeMirror-gutter-elt" style="left: 29px; width: 10px;"><div class="CodeMirror-foldgutter-open CodeMirror-guttermarker-subtle"></div></div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-keyword">if</span> (<span class="cm-keyword">isset</span>(<span class="cm-variable-2">$params</span>[<span class="cm-string">'type'</span>]))</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">3</div><div class="CodeMirror-gutter-elt" style="left: 29px; width: 10px;"><div class="CodeMirror-foldgutter-open CodeMirror-guttermarker-subtle"></div></div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;">{</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">4</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-variable-2">$objTitle</span> <span class="cm-operator">=</span> <span class="cm-variable-2">$this</span><span class="cm-operator">-&gt;</span><span class="cm-variable">object_title</span>;</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">5</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-variable-2">$__Title</span> <span class="cm-operator">=</span> <span class="cm-variable-2">$this</span><span class="cm-operator">-&gt;</span><span class="cm-variable">getProperty</span>(<span class="cm-string">'__Title'</span>);</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">6</div><div class="CodeMirror-gutter-elt" style="left: 29px; width: 10px;"><div class="CodeMirror-foldgutter-open CodeMirror-guttermarker-subtle"></div></div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-keyword">switch</span> (<span class="cm-variable-2">$params</span>[<span class="cm-string">'type'</span>])</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">7</div><div class="CodeMirror-gutter-elt" style="left: 29px; width: 10px;"><div class="CodeMirror-foldgutter-open CodeMirror-guttermarker-subtle"></div></div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span>{</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">8</div></div><pre class=" CodeMirror-line "><span class="cm-tab-wrap-hack" style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-keyword">case</span> <span class="cm-string">"active"</span>:<span class="cm-tab" role="presentation" cm-text="	">  </span></span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">9</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;">      <span class="cm-tab" role="presentation" cm-text="	">  </span><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-variable">say</span> (<span class="cm-string">"Внимание! Устройство "</span>.<span class="cm-variable-2">$__Title</span>.<span class="cm-string">"</span> <span class="cm-string">не активно!"</span>,<span class="cm-number">1</span>);</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">10</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-keyword">break</span>;</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">11</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-keyword">default</span>:</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">12</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-variable">say</span> (<span class="cm-string">"Внимание! У устройства "</span>.<span class="cm-variable-2">$__Title</span>.<span class="cm-string">"</span> <span class="cm-string">зафиксирована неизвестная ошибка!"</span>,<span class="cm-number">1</span>);</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">13</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-tab" role="presentation" cm-text="	">    </span><span class="cm-keyword">break</span>;</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">14</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;"><span class="cm-tab" role="presentation" cm-text="	">    </span>}</span></pre></div><div style="position: relative;"><div class="CodeMirror-gutter-wrapper" style="left: -40px;"><div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 0px; width: 21px;">15</div></div><pre class=" CodeMirror-line "><span style="padding-right: 0.1px;">}</span></pre></div></div></div></div></div></div><div style="position: absolute; height: 13px; width: 1px; top: 308px; border-bottom: 0px solid transparent;"></div><div class="CodeMirror-gutters" style="height: 321px;"><div class="CodeMirror-gutter CodeMirror-linenumbers" style="width: 29px;"></div><div class="CodeMirror-gutter CodeMirror-foldgutter"></div></div></div></div>
					*/?>
				</div>
			</div>

		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-offset-1 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Сохранить">
			&nbsp;
			<a href="<?=$curDir?>class_methods_list.php?id=<?=$classID?>" class="btn btn-default">Отмена</a>
		</div>
	</div>

	<input type="hidden" name="code_code_type" value="0">
	<input type="hidden" name="classID" value="<?=$classID?>">
	<input type="hidden" name="id" value="<?=$methodID?>">
	<input type="hidden" name="action" value="1">
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
