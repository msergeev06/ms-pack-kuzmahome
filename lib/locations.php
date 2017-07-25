<?php

namespace MSergeev\Packages\Kuzmahome\Lib;

use MSergeev\Packages\Kuzmahome\Tables\LocationsTable;

class Locations
{
	public static function getTreeView()
	{
		$html = '';
		$arLocations = LocationsTable::getList(
			array(
				'select' => array('ID','TITLE','DESCRIPTION'),
				'order' => array('TITLE'=>'ASC')
			)
		);
		if ($arLocations)
		{
			$html.='<table class="table table-striped"><tbody>';
			$html.='<tr>';
			$html.='<th width="300">Название</th>';
			$html.='<th width="80%">Описание</th>';
			$html.='<th width="200">&nbsp;</th>';
			$html.='</tr>';

			foreach ($arLocations as $arLocation)
			{
				$html.='<tr>';
				$html.='<td><b>'.$arLocation['TITLE'].'</b></td>';
				$html.='<td><i>'.$arLocation['DESCRIPTION'].'</i></td>';
				$html.='<td>';
				$html.='<a class="btn btn-default btn-sm" href="edit.php?id='.$arLocation['ID'].'"><i class="glyphicon glyphicon-pencil"></i></a>';
				$html.='<a class="btn btn-default btn-sm" href="?mode=delete&id='.$arLocation['ID'].'" onclick="return confirm(\'Вы уверены, что хотите удалить расположение?\')"><i class="glyphicon glyphicon-remove"></i></a>';
				$html.='</td>';
				$html.='</tr>';
			}

			$html.='</tbody></table>';
		}

		return $html;
	}
}