<?php
// 
//     This file is part of Lucterios.
// 
//     Lucterios is free software; you can redistribute it and/or modify
//     it under the terms of the GNU General Public License as published by
//     the Free Software Foundation; either version 2 of the License, or
//     (at your option) any later version.
// 
//     Lucterios is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//     GNU General Public License for more details.
// 
//     You should have received a copy of the GNU General Public License
//     along with Lucterios; if not, write to the Free Software
//     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY

require_once('../CORE/xfer_custom.inc.php');

function addDependValid($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"addDependValid",$Params);
	$name=$Params['name'];
	if (array_key_exists('depend',$Params))
		$depend=$Params['depend'];
	else
		$depend="";
	$version_majeur_max=$Params['version_majeur_max'];
	$version_mineur_max=$Params['version_mineur_max'];
	$version_majeur_min=$Params['version_majeur_min'];
	$version_mineur_min=$Params['version_mineur_min'];
	$optionnal=($Params['optionnal']=='o');

	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	$extension->Depencies[$depend]=new Param_Depencies($name, $version_majeur_max, $version_mineur_max, $version_majeur_min, $version_mineur_min, $optionnal);
	$extension->IncrementBuild();
	return $xfer_result;
}

?>
 
