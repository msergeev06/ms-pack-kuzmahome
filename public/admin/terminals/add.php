<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Терминалы');
use MSergeev\Packages\Kuzmahome\Tables;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$pathTools = MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_TOOLS_ROOT');
$arObjects = Tables\ObjectsTable::getList(
	array(
		'select' => array("TITLE"=>"VALUE","DESCRIPTION"=>"NAME"),
		'order' => array('TITLE'=>'ASC')
	)
);
$arUsers = \MSergeev\Core\Tables\UsersTable::getList(
	array(
		'select' => array('ID'=>'VALUE','LOGIN'=>'NAME'),
		'order' => array('ID'=>'ASC')
	)
);
$arTerminal = array();
if (isset($_POST['action']))
{
	$bError = false;
	if (isset($_POST['NAME']) && strlen($_POST['NAME'])>0)
	{
		$check = Tables\TerminalsTable::getOne(
			array(
				'select' => array('ID'),
				'filter' => array('NAME'=>strtolower($_POST['NAME']))
			)
		);
		if (!$check)
		{
			$arTerminal['NAME'] = strtolower($_POST['NAME']);
		}
		else
		{
			$bError = true;
		}
	}
	else
	{
		$bError = true;
	}
	if (isset($_POST['TITLE']) && strlen($_POST['TITLE'])>0)
	{
		$arTerminal['TITLE'] = $_POST['TITLE'];
	}
	else
	{
		$bError = true;
	}
	if (isset($_POST['HOST']) && strlen($_POST['HOST'])>0)
	{
		$arTerminal['HOST'] = $_POST['HOST'];
	}
	else
	{
		$bError = true;
	}
	if (isset($_POST['USER_ID']) && intval($_POST['USER_ID'])>0)
	{
		$arTerminal['USER_ID'] = intval($_POST['USER_ID']);
	}
	if (isset($_POST['LINKED_OBJECT']) && strlen('LINKED_OBJECT')>0 && strtolower($_POST['LINKED_OBJECT'])!='null')
	{
		$arTerminal['LINKED_OBJECT'] = $_POST['LINKED_OBJECT'];
	}
	if (!$bError)
	{
		$newID = Tables\TerminalsTable::add(array("VALUES"=>$arTerminal))->getInsertId();
		if (!$newID)
		{
			$bError = true;
		}
		else
		{
			\MSergeev\Core\Lib\Buffer::setRefresh($curDir);
		}
	}
}
?>
<br>
<ul class="breadcrumb">
	<li><a href="index.php">Начало</a></li>
	<span class="divider">/</span>
	<li class="active">Новая запись</li>
</ul>
<br><br>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" name="frmEdit">
	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Системное имя:<span style="color:red">*</span></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="NAME" value="<?=$arTerminal['NAME']?>"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Название:<span style="color:red">*</span></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="TITLE" value="<?=$arTerminal['TITLE']?>"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Хост (адрес):<span style="color:red">*</span></label>
		<div class="col-lg-5"><input type="text" class="form-control " name="HOST" value="<?=$arTerminal['HOST']?>"></div>
	</div>

	<div class="form-group ">
		<label class="col-lg-4 control-label" for="inputTitle">Пользователь:</label>
		<div class="col-lg-5">
			<?if($arUsers):?>
				<?=SelectBox('USER_ID',$arUsers,'--- Выбрать ---',(intval($arTerminal['USER_ID'])>0)?$arTerminal['USER_ID']:'null','class="form-control"')?>
			<?endif;?>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-4 control-label">Объект пользователя: <a href="http://majordomo.smartliving.ru/Hints/linked_object_terminal?skin=hint" class="wiki_hint fancybox.iframe"><i class="glyphicon glyphicon-info-sign"></i></a></label>
		<div class="col-lg-5">
			<?if($arObjects):?>
				<?=SelectBox('LINKED_OBJECT',$arObjects,'--- Выбрать ---',(strlen($arTerminal['LINKED_OBJECT'])>0)?$arTerminal['LINKED_OBJECT']:'null','class="form-control"')?>
			<?endif;?>
		</div>
	</div>

	<input type="hidden" name="action" value="1">
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-5">
			<input class="btn btn-default btn-primary" type="submit" name="subm" value="Добавить">
			&nbsp;
			<a href="index.php" class="btn btn-default">Отмена</a>
		</div>
	</div>
</form>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
