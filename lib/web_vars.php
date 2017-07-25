<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Packages\Kuzmahome\Tables\WebVarsTable;

class WebVars
{
	public static function getTreeView ()
	{
		$html = '';

		$arWebVars = WebVarsTable::getList(
			array(
				'select' => array('ID','TITLE','HOSTNAME','LATEST_VALUE','CHECK_NEXT'),
				'order' => array('TITLE'=>'ASC')
			)
		);
		if ($arWebVars)
		{
			$html.='<table class="table table-striped"><tbody>';

			foreach ($arWebVars as $arVar)
			{
				$html.='<tr>';
				$html.='<td valign="top">';
				$html.='<big>'.$arVar['TITLE'].'</big><br>';
				$html.='<b>'.$arVar['HOSTNAME'].'</b>';
				$html.='<br>Next check: '.$arVar['CHECK_NEXT'];
				$html.='</td>';
				$html.='<td valign="top">'.$arVar['LATEST_VALUE'].'</td>';
				$html.='<td width="100">';
				$html.='<a class="btn btn-default btn-sm" href="edit.php?id='.$arVar['ID'].'"><i class="glyphicon glyphicon-pencil"></i></a>';
				$html.='<a class="btn btn-default btn-sm" href="?mode=delete&id='.$arVar['ID'].'" onclick="return confirm(\'Вы уверены? Пожалуйста, подтвердите операцию.\')"><i class="glyphicon glyphicon-remove"></i></a>';
				$html.='</td>';
				$html.='</tr>';
			}

			$html.='</tbody></table>';
		}

		return $html;
	}

	public static function checkAllVars ($bAll=false)
	{
		$arSelect = array(
			'ID',
			'TITLE',
			'HOSTNAME',
			'ENCODING',
			'SEARCH_PATTERN',
			'LATEST_VALUE',
			'CHECK_INTERVAL',
			'CHECK_LATEST',
			'CHECK_NEXT',
			'LINKED_OBJECT',
			'LINKED_PROPERTY',
			'SCRIPT_ID',
			'CODE',
			'NEED_AUTH',
			'AUTH_USERNAME',
			'AUTH_PASSWORD'
		);
		if ($bAll)
		{
			$arWebVars = WebVarsTable::getList(
				array(
					'select' => $arSelect,
					'order' => array('TITLE'=>'ASC')
				)
			);
		}
		else
		{
			$arWebVars = WebVarsTable::getList(
				array(
					'select' => $arSelect,
					'filter' => array('<=CHECK_NEXT'=>date('d.m.Y H:i:s'),'CHECK_NEXT'=>NULL),
					'filter_logic'=>'OR',
					'order' => array('CHECK_NEXT'=>'ASC')
				)
			);
		}

		if ($arWebVars)
		{
			foreach ($arWebVars as $arVar)
			{
				if (!$arVar['HOSTNAME']) {
					continue;
				}
				if (!$arVar['CHECK_INTERVAL'])
				{
					$arVar['CHECK_INTERVAL']=60;
				}
				$arVar['CHECK_NEXT']=date('d.m.Y H:i:s', time()+$arVar['CHECK_INTERVAL']);
				$latestValue = $arVar['LATEST_VALUE'];
				if ($arVar['NEED_AUTH'] && $arVar['AUTH_USERNAME'])
				{
					$content=Http::getURL($arVar['HOSTNAME'], $arVar['CHECK_INTERVAL'], $arVar['AUTH_USERNAME'], $arVar['AUTH_PASSWORD']);
				}
				else
				{
					$content=Http::getURL($arVar['HOSTNAME'], $arVar['CHECK_INTERVAL']);
				}
				if ($arVar['ENCODING']!='' && strtoupper($arVar['ENCODING'])!='UTF-8')
				{
					$content=iconv($arVar['ENCODING'], "UTF-8", $content);
				}
				$bOk = true;
				$newValue = '';
				if ($arVar['SEARCH_PATTERN'])
				{
					if (preg_match('/'.$arVar['SEARCH_PATTERN'].'/is', $content, $m))
					{
						$total1=count($m);
						for($i1=1;$i1<$total1;$i1++)
						{
							$newValue.=$m[$i1];
						}
					}
					else
					{
						$bOk=false; // result did not matched
					}
				}
				else
				{
					$newValue=$content;
				}
				if (strlen($newValue)>50*1024)
				{
					$newValue=substr($newValue, 0, 50*1024);
				}
				if (!$bOk)
				{
					$primID = $arVar['ID'];
					unset($arVar['ID']);
					WebVarsTable::update($primID,array("VALUES"=>$arVar));
					continue;
				}

				$arVar['CHECK_LATEST']=date('d.m.Y H:i:s');
				$arVar['LATEST_VALUE']=$newValue;
				if ($arVar['LINKED_OBJECT']!='' && $arVar['LINKED_PROPERTY']!='')
				{
					Objects::getObject($arVar['LINKED_OBJECT'])->setProperty($arVar['LINKED_PROPERTY'], $newValue);
				}

				if ($latestValue!=$newValue && $latestValue!='')
				{
					$params=array('VALUE'=>$newValue);
					// do some status change actions
					$run_script_id=0;
					$run_code='';
					// got online
					if ($arVar['SCRIPT_ID'])
					{
						$run_script_id=$arVar['SCRIPT_ID'];
					}
					elseif ($arVar['CODE'])
					{
						$run_code=$arVar['CODE'];
					}

					if ($run_script_id)
					{
						//run script
						Scripts::runScript($run_script_id, $params);
					}
					elseif ($run_code)
					{
						//run code
						try
						{
							$code=$run_code;
							$success=eval($code);
							if ($success===false)
							{
								Logs::debMes("Error in webvar code: ".$code);
								//registerError('webvars', "Error in webvar code: ".$code);
							}
						}
						catch(\Exception $e)
						{
							Logs::debMes('Error: exception '.get_class($e).', '.$e->getMessage().'.');
							//registerError('webvars', get_class($e).', '.$e->getMessage());
						}
					}
				}
				$primID = $arVar['ID'];
				unset($arVar['ID']);
				WebVarsTable::update($primID,array("VALUES"=>$arVar));
			}
		}
	}
}