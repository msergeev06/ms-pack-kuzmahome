<?php

use MSergeev\Packages\Kuzmahome\Lib;
use MSergeev\Core\Lib as CoreLib;

$EnergyTariff = CoreLib\Options::getOptionStr('KUZMAHOME_ENERGY_TARIFF');
$sayT1 = CoreLib\Options::getOptionStr('KUZMAHOME_SAY_T1');
$sayT2 = CoreLib\Options::getOptionStr('KUZMAHOME_SAY_T2');
$sayT3 = CoreLib\Options::getOptionStr('KUZMAHOME_SAY_T3');
//T1 = 6.41, T2 = 1.64, T3 = 5.32, O = 5.38
//T1 = пик, Т2 = льготный, Т3 = полупик
if (Lib\DateTime::timeBetween('7:00', '9:59') || Lib\DateTime::timeBetween('17:00', '20:59'))
{
	if ($EnergyTariff != 'T1')
	{
		CoreLib\Options::setOption('KUZMAHOME_ENERGY_TARIFF','T1');
		Lib\Say::sayTariff($sayT1,1);
	}
}

if (Lib\DateTime::timeBetween('23:00', '6:59'))
{
	if ($EnergyTariff != 'T2')
	{
		CoreLib\Options::setOption('KUZMAHOME_ENERGY_TARIFF','T2');
		Lib\Say::sayTariff($sayT2,2);
	}
}

if (Lib\DateTime::timeBetween('10:00', '16:59') || Lib\DateTime::timeBetween('21:00', '22:59'))
{
	if ($EnergyTariff != 'T3')
	{
		CoreLib\Options::setOption('KUZMAHOME_ENERGY_TARIFF','T3');
		Lib\Say::sayTariff($sayT3,1);
	}
}
