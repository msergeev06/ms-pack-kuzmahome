<? include_once(__DIR__."/../include/header.php");
use MSergeev\Core\Lib as CoreLib;
use MSergeev\Packages\Kuzmahome\Lib;
CoreLib\Buffer::setTitle('Авторизация');
$path= CoreLib\Loader::getSitePublic('kuzmahome');
global $USER;
if (isset($_POST['refresh']))
{
	$refreshAddr = $_POST['refresh'];
}
else
{
	$refreshAddr = $path.'admin/';
}
//echo '<pre>',$refreshAddr,"\n",$_SERVER['HTTP_REFERER'],"\n",print_r($_POST,true),'</pre>';
if (!isset($_REQUEST['act']))
{
	$act = $_REQUEST['act'] = 'login';
}
else
{
	$act = $_REQUEST['act'];
}
$action = intval($_POST['action']);
if($act=='login' && $action==1)
{
	if (intval($_POST['remember'])>0)
	{
		$remember = true;
	}
	else
	{
		$remember = false;
	}
	$result = Lib\Users::logIn($_POST['login'],$_POST['password'],$remember);
	if (!$result)
	{
		$action = 0;
	}
	else
	{
		CoreLib\Buffer::setRefresh($refreshAddr);
	}
}
elseif ($act=='logout')
{
	$USER->logOut();
	$action=1;
}

?>
<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-6 center-block">
	<?if($act=='login' && $action!=1):?>
		<?if(!$USER->isAuthorise()):?>
			<h3>Авторизация</h3>
			<form role="form" method="post" action="">
				<div class="form-group">
					<label for="inputLogin">Логин</label>
					<input type="text" name="login" class="form-control" id="inputLogin" placeholder="Введите логин">
				</div>
				<div class="form-group">
					<label for="inputPassword">Пароль</label>
					<input type="password" name="password" class="form-control" id="inputPassword" placeholder="Пароль">
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="remember" value="1"> Запомнить меня
					</label>
				</div>
				<input type="hidden" name="action" value="1">
				<input type="hidden" name="act" value="<?=$_REQUEST['act']?>">
				<input type="hidden" name="refresh" value="<?=$_SERVER['HTTP_REFERER']?>">
				<button type="submit" class="btn btn-default">Войти</button>
			</form>
		<?endif;?>
		<?if(isset($result) && !$result){?>
			<p class="text-danger">Ошибка авторизации. Попробуйте ввести данные заново</p>
		<?}elseif((isset($result) && $result) || $USER->isAdmin()){?>
			<p class="text-success">Вы успешно авторизовались. Можете продолжать работу</p>
			<? CoreLib\Buffer::setRefresh($refreshAddr,3)?>
		<?}?>
	<?elseif($act == 'logout' && $action==1):?>
		<p class="text-success">Вы успешно вышли.</p>
		<? CoreLib\Buffer::setRefresh($refreshAddr,3)?>
	<?endif;?>
	</div>
	<div class="col-md-3"></div>

</div>
<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
