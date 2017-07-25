<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Core\Lib as CoreLib;
use MSergeev\Core\Entity\Query;
use MSergeev\Packages\Kuzmahome\Tables;

class Scripts
{
	public static function runScript($id, $params = '')
	{
		$query = new Query('select');
		$sqlHelper = new CoreLib\SqlHelper(Tables\ScriptsTable::getTableName());
		$sql = "SELECT\n\t*\nFROM\n\t"
			.$sqlHelper->wrapTableQuotes()."\n"
			."WHERE\n\t"
			.$sqlHelper->wrapFieldQuotes('ID')."='" . (int)$id . "'\n\t OR\n\t"
			.$sqlHelper->wrapFieldQuotes('TITLE')." LIKE '" . $id . "'";
		$query->setQueryBuildParts($sql);
		$res = $query->exec();
		if ($ar_res = $res->fetch())
		{
			//msDebug($ar_res);
			$arUpdate = array(
				'EXECUTED' => date('d.m.Y H:i:s')
			);
			if ($params)
			{
				$arUpdate['EXECUTED_PARAMS'] = $params;
			}
			Tables\ScriptsTable::update($ar_res['ID'],array("VALUES"=>$arUpdate));

			try {
				$success = eval($ar_res['CODE']);
				if ($success === false)
				{
					Logs::debMes(sprintf('Error in script "%s". Code: %s', $ar_res['TITLE'], $ar_res['CODE']));
				}
			}
			catch (\Exception $e)
			{
				Logs::debMes(sprintf('Error in script "%s": '.$e->getMessage(), $ar_res['TITLE']));
			}
		}
	}

	public static function clone_script($id)
	{

		//$rec=SQLSelectOne("SELECT * FROM scripts WHERE ID='".(int)$id."'");
		$rec = Tables\ScriptsTable::getList(
			array(
				'filter' => array('ID'=>intval($id)),
				'limit' => 1
			)
		);
		if ($rec && isset($rec[0]))
		{
			$rec = $rec[0];
		}
		$rec['TITLE'].='_copy';
		unset($rec['ID']);
		unset($rec['EXECUTED']);
		//$rec['ID']=SQLInsert('scripts', $rec);
		$rec['ID'] = Tables\ScriptsTable::add(array("VALUES"=>$rec))->getInsertId();

		return $rec['ID'];
	}

	public static function delete_scripts($id)
	{
		//$rec=SQLSelectOne("SELECT * FROM scripts WHERE ID='$id'");
		// some action for related tables
		//SQLExec("DELETE FROM scripts WHERE ID='".$rec['ID']."'");
		Tables\ScriptsTable::delete(intval($id));
	}

	public static function checkScheduledScripts()
	{
		//$scripts=SQLSelect("SELECT ID, TITLE, RUN_DAYS, RUN_TIME FROM scripts WHERE RUN_PERIODICALLY=1 AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(EXECUTED))>1200");
		$query = new Query('select');
		$sqlHelp = new CoreLib\SqlHelper(Tables\ScriptsTable::getTableName());
		$sql = "SELECT\n\t"
			.$sqlHelp->wrapFieldQuotes('ID').", "
			.$sqlHelp->wrapFieldQuotes('TITLE').", "
			.$sqlHelp->wrapFieldQuotes('RUN_DAYS').", "
			.$sqlHelp->wrapFieldQuotes('RUN_TIME')."\nFROM\n\t"
			.$sqlHelp->wrapTableQuotes()."\nWHERE\n\t"
			.$sqlHelp->wrapFieldQuotes('RUN_PERIODICALLY')."='Y' AND\n\t(UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP("
			.$sqlHelp->wrapFieldQuotes('EXECUTED')."))>1200";
		$query->setQueryBuildParts($sql);
		$scripts = array();
		$res = $query->exec();
		while ($ar_res = $res->fetch())
		{
			$scripts[] = $ar_res;
		}

		$total=count($scripts);
		for($i=0;$i<$total;$i++)
		{

			$rec=$scripts[$i];

			if ($rec['RUN_DAYS']==='')
			{
				continue;
			}

			$run_days=explode(',', $rec['RUN_DAYS']);
			if (!in_array(date('w'), $run_days))
			{
				continue;
			}

			$tm=strtotime(date('Y-m-d').' '.$rec['RUN_TIME']);

			$diff=time()-$tm;

			if ($diff<0 || $diff>=10*60)
			{
				continue;
			}

			static::runScript($rec['TITLE']);

			$rec['DIFF']=$diff;

			//print_r($rec);

		}
		//print_r($scripts);
	}

	public static function delete_categories($id)
	{
		//$rec=SQLSelectOne("SELECT * FROM script_categories WHERE ID='$id'");
		// some action for related tables
		//SQLExec("UPDATE scripts SET CATEGORY_ID=0 WHERE CATEGORY_ID='".$rec['ID']."'");
		$query = new Query('update');
		$sqlHelp = new CoreLib\SqlHelper(Tables\ScriptsTable::getTableName());
		$sql = "UPDATE\n\t"
			.$sqlHelp->wrapTableQuotes()."\nSET\n\t"
			.$sqlHelp->wrapFieldQuotes('CATEGORY_ID')."=0\nWHERE\n\t"
			.$sqlHelp->wrapFieldQuotes('CATEGORY_ID')."='".intval($id)."'";
		$query->setQueryBuildParts($sql);
		$query->exec();
		//SQLExec("DELETE FROM script_categories WHERE ID='".$rec['ID']."'");
		Tables\ScriptsTable::delete(intval($id));
	}

	public static function log ($strMessage)
	{
		$logDir = Logs::getLogsDir();
		$today_file = $logDir . 'log-scripts_' . date('Y-m-d') . '.txt';
		$f1 = fopen ($today_file, 'a');
		$tmp=explode(' ', microtime());
		fwrite($f1, date("H:i:s ").$tmp[0].' '.$strMessage."\n------------------\n");
		fclose ($f1);
		@chmod($today_file, Files::getFileChmod());
	}

	public static function getTreeView()
	{
		$html = '';
		global $USER;

		$arRes = Tables\ScriptsTable::getList(
			array(
				'select' => array('ID','TITLE','DESCRIPTION','CATEGORY_ID','CATEGORY_ID.TITLE'=>'CATEGORY_TITLE'),
				'order' => array('CATEGORY_ID.TITLE'=>'ASC','TITLE'=>'ASC')
			)
		);
		if (!$arRes) return $html;
		$arScripts = array();
		foreach ($arRes as $ar_res)
		{
			if (!isset($arScripts[intval($ar_res['CATEGORY_ID'])]))
			{
				$arScripts[intval($ar_res['CATEGORY_ID'])]['TITLE'] = $ar_res['CATEGORY_TITLE'];
			}
			$arScripts[intval($ar_res['CATEGORY_ID'])]['SCRIPTS'][] = $ar_res;
		}
		//msDebug($arScripts);

		foreach ($arScripts as $categoryID=>$arCategory)
		{
			$html.='<big><b>';
			if (intval($categoryID)==0)
			{
				$html.='Другое';
			}
			else
			{
				$html.=$arCategory['TITLE'];
			}
			$html.='</b></big>';
			if (
				$USER->issetUserCookie('scripts-category-view-'.$categoryID)===true
				&& intval($USER->getUserCookie('scripts-category-view-'.$categoryID))==1
			)
			{
				$bShow = true;
			}
			else
			{
				$bShow = false;
			}
			$html.='<a href="#" id="link-'.intval($categoryID).'" data-comm="';
			if ($bShow)
			{
				$html.='hide';
			}
			else
			{
				$html.='show';
			}
			$html.='" style="text-decoration:none" onclick="return showHide('.intval($categoryID).');">';
			if ($bShow)
			{
				$html.='[ - ]';
			}
			else
			{
				$html.='[ + ]';
			}
			$html.='</a><br>';
			$html.='<div id="category-'.intval($categoryID).'" class="';
			if ($bShow)
			{
				$html.='show';
			}
			else
			{
				$html.='hide';
			}
			$html.='">';
			if (isset($arCategory['SCRIPTS']) && !empty($arCategory['SCRIPTS']))
			$html.='<table class="table table-striped">';
			$html.='<tbody>';
			foreach($arCategory['SCRIPTS'] as $arScript)
			{
				$html.='<tr class="hover_btn2">';
				$html.='<td nowrap=""><b><a href="edit_script.php?id='.$arScript['ID'].'">'.$arScript['TITLE'].'</a></b></td>';
				$html.='<td width="90%">'.$arScript['DESCRIPTION'].'&nbsp;</td>';
				$html.='<td width="200" nowrap="">';
				$html.='<a href="edit_script.php?id='.$arScript['ID'].'" class="btn btn-default btn-sm" title="Редактировать"><i class="glyphicon glyphicon-pencil"></i></a>';
				$html.='<a href="?mode=run&id='.$arScript['ID'].'" target="_blank" onclick="return confirm(\'Вы уверены? Пожалуйста, подтвердите операцию.\')" class="btn btn-default btn-sm" title="Выполнить"><i class="glyphicon glyphicon-flash"></i></a>';
				$html.='<a href="?mode=delete&id='.$arScript['ID'].'" onclick="return confirm(\'Вы уверены? Пожалуйста, подтвердите операцию.\')" class="btn btn-default btn-sm" title="Удалить"><i class="glyphicon glyphicon-remove"></i></a>';
				$html.='</td>';
				$html.='</tr>';
			}
			$html.='</tbody>';
			$html.='</table>';
			$html.='</div>';
		}


		return $html;
	}

	public static function showCodemirrorScript ($codeField='code')
	{
		?>
		<script language="javascript">
			var myTextAreacode;
			$(document).ready(function(){
				var myTextAreacode=document.getElementById('<?=$codeField?>');
				var editor = CodeMirror.fromTextArea(myTextAreacode, {
					value: myTextAreacode.value,
					lineNumbers: true,
					matchBrackets: true,

					mode: "text/x-php",

					indentUnit: 3,
					tabSize: 3,
					firstLineNumber: 1,
					indentWithTabs: false,
					autoCloseBrackets: true,
					foldGutter: true,
					gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
					extraKeys: {
						"F11": function(cm) {
							cm.setOption("fullScreen", !cm.getOption("fullScreen"));
						},
						"Ctrl-S": function(instance) { document.getElementById('code').form.submit(); },
						"Esc": function(cm) {
							if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
						},
						"Ctrl-Space": "autocomplete",
						"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }
					}
				});
			});
		</script>
	<?
	}
}