<? include_once(__DIR__."/../../include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Текстовые файлы - Редактирование');
use MSergeev\Packages\Kuzmahome\Tables;
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
if (!$USER->isAdmin()) die();
$curPage = \MSergeev\Core\Lib\Tools::getCurPath();
$curDir = \MSergeev\Core\Lib\Tools::getCurDir();
$pathTools = \MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_TOOLS_ROOT');
$dirTextFiles = \MSergeev\Core\Lib\Config::getConfig('DIR_TEXT_FILES');
$arFile = array();
$bShowForm = true;
if (isset($_REQUEST['file']) && $_REQUEST['file']!='')
{
	$_REQUEST['file'] = strtolower($_REQUEST['file']);
	if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='delete')
	{
		if (file_exists($dirTextFiles.$_REQUEST['file'].'.txt'))
		{
			unlink($dirTextFiles.$_REQUEST['file'].'.txt');
			\MSergeev\Core\Lib\Buffer::setRefresh($curDir);
			$bShowForm = false;
		}
	}
	if (isset($_POST['action']) && intval($_POST['action'])==1 && isset($_POST['data']) && $_POST['data']!='')
	{
		Lib\Files::saveFile($dirTextFiles.$_REQUEST['file'].'.txt',$_POST['data']);
		\MSergeev\Core\Lib\Buffer::setRefresh($curDir);
		$bShowForm = false;
	}
	if (file_exists($dirTextFiles.$_REQUEST['file'].'.txt'))
	{
		$arFile = pathinfo($dirTextFiles.$_REQUEST['file'].'.txt');
		$arFile['data'] = Lib\Files::loadFile($dirTextFiles.$_REQUEST['file'].'.txt');
	}
}
?>
<?if($bShowForm):?>
<ul class="breadcrumb">
	<li><a href="<?=$curDir?>">Начало</a></li>
	<span class="divider">/</span>
	<li class="active">Редактирование записи</li>
</ul>
<br>
<form action="" method="post" class="form-horizontal">
	<div class="form-group ">
		<label class="col-lg-2 control-label" for="inputTitle">Название:</label>
		<div class="col-lg-5">
			<input type="text" class="form-control" name="file" value="<?=$arFile['filename']?>">
		</div>
	</div>

	<div class="form-group ">
		<label class="col-lg-2 control-label" for="inputTitle">Данные:</label>
		<div class="col-lg-5">
			<textarea name="data" class="form-control" rows="30"><?=$arFile['data']?></textarea>
		</div>
	</div>

	<input type="hidden" name="action" value="1">

	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-5">
			<input type="submit" name="submit" value="Сохранить" class="btn btn-primary">
			<a href="<?=$curDir?>" class="btn  btn-default">Отмена</a>
			<a href="?mode=delete&file=<?=$arFile['filename']?>" class="btn  btn-default" onclick="return confirm('Вы уверены? Пожалуйста, подтвердите операцию.')">Удалить</a>
		</div>
	</div>
</form>
<br>
<?if(isset($arFile['filename']) && $arFile['filename']!=''):?>
<div class="col-lg-offset-2 col-lg-5">
	Пример использования: getRandomLine('<?=$arFile['filename']?>');
</div>
<?endif;?>
<?endif;?>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
