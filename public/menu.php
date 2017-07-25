<? include_once(__DIR__."/include/header.php"); MSergeev\Core\Lib\Buffer::setTitle('Меню');
use MSergeev\Packages\Kuzmahome\Lib;
global $USER;
$templPath = MSergeev\Core\Lib\Loader::getSiteTemplate("kuzmahome");
$path = MSergeev\Core\Lib\Loader::getSitePublic("kuzmahome");
$pathTools = MSergeev\Core\Lib\Config::getConfig('KUZMAHOME_TOOLS_ROOT');
if (!isset($_REQUEST['list']))
{
?>
<ul class="list-group">
	<li class="list-group-item">
		<div class="row">
			<div class="col-md-6">
				<div style="font-size:24px"><span class="time-now"><?=date('H:i')?></span>&nbsp;
					<img src="<?=$templPath?>images/network_32_<?=getGlobal('propNetworkStatus')?>.png" align="absmiddle">
				</div>
				<script type="text/javascript">
					var timerId = setInterval(function(){
						var date = new Date();
						var h = date.getHours();
						var m = date.getMinutes();
						if (h<10) h = '0'+h;
						if (m<10) m = '0'+m;
						$('.time-now').html(h+':'+m);
					}, 1000);
				</script>
			</div>
			<div class="col-md-6" style="text-align: right"><i class="glyphicon glyphicon-lock" style="float:right;"></i>&nbsp;<?=getGlobal(Lib\Users::getUserObject($USER->getID()).'.propFullName')?>&nbsp;</div>
		</div>
	</li>
	<li class="list-group-item">
		<span id="list1" style="color:<?=getGlobal('user_msergeev.propColor')?>;">
			<span id="list1-name"><?=getGlobal('user_msergeev.propFullName')?></span> --
			<span id="list1-point"><?=getGlobal('user_msergeev.propSeenAt')?></span>
			(<span id="list1-time"><?=getGlobal('user_msergeev.propCoordinatesUpdated')?></span>)<br>
			Заряд мобильного <span id="list1-energy"><?=getGlobal('user_msergeev.propBattLevel')?></span>%
		</span>
	</li>
	<li class="list-group-item">
		<span style="color:<?=getGlobal('user_duhfeniksa.propColor')?>;">
			<?=getGlobal('user_duhfeniksa.propFullName')?> --
			<?=getGlobal('user_duhfeniksa.propSeenAt')?>
			(<?=getGlobal('user_duhfeniksa.propCoordinatesUpdated')?>)<br>
			Заряд мобильного <?=getGlobal('user_duhfeniksa.propBattLevel')?>%
		</span>
	</li>
	<li class="list-group-item">
		<span style="color:<?=getGlobal('user_nastya.propColor')?>;">
			<?=getGlobal('user_nastya.propFullName')?> --
			<?=getGlobal('user_nastya.propSeenAt')?>
			(<?=getGlobal('user_nastya.propCoordinatesUpdated')?>)<br>
			Заряд мобильного <?=getGlobal('user_nastya.propBattLevel')?>%
		</span>
	</li>
	<li class="list-group-item" style="padding:0;">
		<a href="?list=1" style="text-decoration: none;"><button type="button" class="btn btn-default btn-block btn-lg" style="height:50px;"><span style="float:left;">Климат (???°C)</span><i class="glyphicon glyphicon-circle-arrow-right" style="float:right;"></i></button></a>
	</li>
	<li class="list-group-item" style="padding:0;">
		<a href="?list=2" style="text-decoration: none;"><button type="button" class="btn btn-default btn-block btn-lg" style="height:50px;"><span style="float:left;">Учет</span><i class="glyphicon glyphicon-circle-arrow-right" style="float:right;"></i></button></a>
	</li>
	<li class="list-group-item" style="padding:0;">
		<a href="#" style="text-decoration: none;" onclick="return showHideList('list3');"><button type="button" class="btn btn-default btn-block btn-lg" style="height:50px;outline:none;"><span style="float:left;">Настройки</span><i class="glyph-list3 glyphicon glyphicon-plus-sign" style="float:right;"></i></button></a>
		<ul class="list-group" id="list3" style="display:none;">
			<li class="list-group-item">
				Подменю 3
			</li>
		</ul>
	</li>
	<li class="list-group-item" style="padding:0;">
		<a href="#" style="text-decoration: none;" onclick="return showHideList('list4');"><button type="button" class="btn btn-default btn-block btn-lg" style="height:50px;outline:none;"><span style="float:left;">Фразы</span><i class="glyph-list4 glyphicon glyphicon-plus-sign" style="float:right;"></i></button></a>
		<ul class="list-group" id="list4" style="display:none;">
			<li class="list-group-item">
				Подменю 4
			</li>
		</ul>
	</li>
	<li class="list-group-item" style="padding:0;">
		<a href="#" style="text-decoration: none;" onclick="return funcList5();"><button type="button" class="btn btn-default btn-block btn-lg" style="height:50px;"><span style="float:left;">JavaScript команда</span><i class="glyphicon glyphicon-circle-arrow-right" style="float:right;"></i></button></a>
		<script type="text/javascript">
			function funcList5 (){
				alert('funcList5');

				return false;
			}
		</script>
	</li>
	<li class="list-group-item">
		<select name="menu166_v1" id="menu166_v1" onchange="changedValue166();" style="width:50px;height:50px;font-size:14px;">
			<option value="00" selected="">00</option>
			<option value="01">01</option>
			<option value="02">02
			</option><option value="03">03
			</option><option value="04">04
			</option><option value="05">05
			</option><option value="06">06
			</option><option value="07">07
			</option><option value="08">08
			</option><option value="09">09
			</option><option value="10">10
			</option><option value="11">11
			</option><option value="12">12
			</option><option value="13">13
			</option><option value="14">14
			</option><option value="15">15
			</option><option value="16">16
			</option><option value="17">17
			</option><option value="18">18
			</option><option value="19">19
			</option><option value="20">20
			</option><option value="21">21
			</option><option value="22">22
			</option><option value="23">23
			</option></select>
			<select name="menu166_v2" id="menu166_v2" onchange="changedValue166();" style="width:50px;height:50px;font-size:14px;">
						<option value="00" selected="">00
						</option><option value="01">01
						</option><option value="02">02
						</option><option value="03">03
						</option><option value="04">04
						</option><option value="05">05
						</option><option value="06">06
						</option><option value="07">07
						</option><option value="08">08
						</option><option value="09">09
						</option><option value="10">10
						</option><option value="11">11
						</option><option value="12">12
						</option><option value="13">13
						</option><option value="14">14
						</option><option value="15">15
						</option><option value="16">16
						</option><option value="17">17
						</option><option value="18">18
						</option><option value="19">19
						</option><option value="20">20
						</option><option value="21">21
						</option><option value="22">22
						</option><option value="23">23
						</option><option value="24">24
						</option><option value="25">25
						</option><option value="26">26
						</option><option value="27">27
						</option><option value="28">28
						</option><option value="29">29
						</option><option value="30">30
						</option><option value="31">31
						</option><option value="32">32
						</option><option value="33">33
						</option><option value="34">34
						</option><option value="35">35
						</option><option value="36">36
						</option><option value="37">37
						</option><option value="38">38
						</option><option value="39">39
						</option><option value="40">40
						</option><option value="41">41
						</option><option value="42">42
						</option><option value="43">43
						</option><option value="44">44
						</option><option value="45">45
						</option><option value="46">46
						</option><option value="47">47
						</option><option value="48">48
						</option><option value="49">49
						</option><option value="50">50
						</option><option value="51">51
						</option><option value="52">52
						</option><option value="53">53
						</option><option value="54">54
						</option><option value="55">55
						</option><option value="56">56
						</option><option value="57">57
						</option><option value="58">58
						</option><option value="59">59
						</option></select>
<?/*		<script language="javascript">
			/*
			var item166_timer=0;
			function changedValue166() {
				if (valueChangedFlag['item166']==1) {
					valueChangedFlag['item166']=0;
					return;
				}
				clearTimeout(item166_timer);
				var elem1=document.getElementById('menu166_v1');
				var elem2=document.getElementById('menu166_v2');
				item166_timer=setTimeout('itemValueChanged("166", "'+elem1.value+':'+elem2.value+'")', 500);
				return false;
			}
			function itemValueChanged(id, new_value) {
				//alert(id+': '+new_value);
				valuesCollected=valuesCollected.replace(','+id+',', ',');
				var url="/ajax/commands.html?op=value_changed";
				if ($('#processing_'+id).length) {
					$('#processing_'+id).html(' - ...');
				}
		</script>			*/?>

	</li>
	<li class="list-group-item">
		<input class='inputcolor' type="color" name="testcolor" id="testcolor" value="" onchange="return setcolor('testcolor');" style="width: 50px;height:50px;"><span class="colorvalue-testcolor"></span>
	</li>
	<li class="list-group-item">
		<a href="#" class="switch-on-off on" data-status="on" id="switch-list6" onclick="return toggleSwitch('switch-list6');"></a>
	</li>
	<li class="list-group-item">
		<input type="date" name="date" value="" style="height:35px;">
	</li>
	<li class="list-group-item">
		<button type="button" id="list8" data-name="Кнопка" class="btn btn-default btn-block btn-lg" style="height:50px;outline:none;" onclick="return clickButton('list8');">Кнопка</button>
	</li>
	<li class="list-group-item">
		<button type="button" id="list9" data-name="Кнопка 2" class="btn btn-default btn-block btn-lg" style="height:50px;outline:none;" onclick="return clickButton('list9');">Кнопка 2</button>
	</li>
	<li class="list-group-item" style="padding:0;">
		<a href="?list=1" style="text-decoration: none;"><button type="button" class="btn btn-default btn-block btn-lg" style="height:50px;"><span style="float:left;">Новое окно</span><i class="glyphicon glyphicon-circle-arrow-right" style="float:right;"></i></button></a>
	</li>
	<li class="list-group-item">
		Плюс-минус<br><br>
		<button type="button" id="list11-minus" data-step="1" data-min="-1" class="btn btn-default" style="height:50px;outline:none;" onclick="return clickMinusButton('list11');">-</button>&nbsp;
		<input type="text" name="list11-plus-minus" id='list11-plus-minus' value="0" style="width:100px;">&nbsp;
		<button type="button" id="list11-plus" data-step="1" <?/*data-max="10" */?>class="btn btn-default" style="height:50px;outline:none;" onclick="return clickPlusButton('list11');">+</button>
	</li>
	<li class="list-group-item">
	</li>

	<li class="list-group-item" role="heading" style="background-color: rgb(233, 233, 233);height:40px;">История событий</li>
	<li class="list-group-item" id="history"><?=Lib\Say::showSaidMessages()?></li>
	<li class="list-group-item">
		<input type="text" style="width:100%;height:40px;font-size:18px;" name="command" value="" placeholder="Введите команду">
	</li>
	<li class="list-group-item">
		<button type="button" class="btn btn-default btn-block btn-lg" onclick="return sendCommand(<?=$USER->getID()?>);">Отправить</button>
	</li>
	<script type="text/javascript">
		$(document).ready(function(){
			$('input[name=command]').keyup(function(e) {
				var userID = <?=$USER->getID()?>;
				if (e.keyCode === 13) {
					sendCommand(userID);
				}
			});
			setInterval(function(){
				updateHistory();
			}, 5000);
		});
		function updateHistory ()
		{
			var field = $('#history');
			$.ajax({
				type: "POST",
				url: '<?=$pathTools?>ajax/update_history.php',
				data: {
					update: 1
				},
				success: function(data){
					field.html(data.html);
				},
				dataType: 'json'
			});
		}
		function sendCommand (userID)
		{
			var input = $('input[name=command]');
			var commandText = input.val();

			$.ajax({
				type: "POST",
				url: '<?=$pathTools?>ajax/send_command.php',
				data: {
					userID: userID,
					command: commandText
				},
				success: function(data){
					input.val('');
					updateHistory();
				},
				dataType: 'json'
			});
		}
	</script>
</ul>
	<script type="text/javascript">
		function setcolor (id)
		{
			$('.colorvalue-'+id).html($('#'+id).val());
		}
		function toggleSwitch (classID)
		{
			var item = $('#'+classID);
			//if (item.data.status == 'on')
			if (item.hasClass('on'))
			{
				item.removeClass('on');
				item.addClass('off');
				//item.data.status = 'off';
			}
			else
			{
				item.removeClass('off');
				item.addClass('on');
				//item.data.status = 'on';
			}
			return false;
		}
		function clickButton (listID)
		{
			var button = $('#'+listID);
			var buttonName = button.data('name');
			button.text(buttonName+' - ...');
			setTimeout(function(){
				var button = $('#'+listID);
				var buttonName = button.data('name');
				button.text(buttonName+' - OK');
				setTimeout(function(){
					var button = $('#'+listID);
					var buttonName = button.data('name');
					button.text(buttonName);
				}, 5000);
			}, 5000);
		}
		function clickMinusButton (id)
		{
			var input = $('#'+id+'-plus-minus');
			var button = $('#'+id+'-minus');
			var step = Number(button.data('step'));
			var val = Number(input.val());
			var stop = Number(button.data('min'));
			if (isNaN(stop))
			{
				input.val(val - step);
			}
			else if (val>stop)
			{
				input.val(val - step);
			}
		}
		function clickPlusButton (id)
		{
			var input = $('#'+id+'-plus-minus');
			var button = $('#'+id+'-plus');
			var step = Number(button.data('step'));
			var val = Number(input.val());
			var stop = Number(button.data('max'));
			if (isNaN(stop))
			{
				input.val(val + step);
			}
			else if (val<stop)
			{
				input.val(val + step);
			}
		}
		function showHideList(listID)
		{
			if (document.getElementById(listID).style.display == "none")
			{
				document.getElementById(listID).style.display = "block";
				$('.glyph-'+listID).removeClass('glyphicon-plus-sign').addClass('glyphicon-minus-sign');
			}
			else
			{
				document.getElementById(listID).style.display = "none";
				$('.glyph-'+listID).removeClass('glyphicon-minus-sign').addClass('glyphicon-plus-sign');
			}

			return false;
		}
	</script>
<?
}
elseif($_REQUEST['list']==1)
{
?>
<ul class="list-group">
	<li class="list-group-item" style="padding:0;">
		<a href="?" style="text-decoration: none;"><button type="button" class="btn btn-default btn-block btn-lg" style="height:50px;"><i class="glyphicon glyphicon-circle-arrow-left" style="float:left;"></i><span style="float:left;">&nbsp;Назад</span></button></a>
	</li>
</ul>
<?
}
elseif($_REQUEST['list']==2)
{
	?>
	<ul class="list-group">
		<li class="list-group-item" style="padding:0;">
			<a href="?" style="text-decoration: none;"><button type="button" class="btn btn-default btn-block btn-lg" style="height:50px;"><i class="glyphicon glyphicon-circle-arrow-left" style="float:left;"></i><span style="float:left;">&nbsp;Назад</span></button></a>
		</li>
	</ul>
<?
}
?>

<? $curDir = basename(__DIR__); ?>
<? include_once(MSergeev\Core\Lib\Loader::getPublic("kuzmahome")."include/footer.php"); ?>
