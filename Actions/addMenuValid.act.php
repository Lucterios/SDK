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

function addMenuValid($Params,$extensionname)
{
	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	$xfer_result=new Xfer_Container_Acknowledge($extensionname,"addMenuValid",$Params);
	foreach($Params as $name=>$value)
		$$name=$value;
	if ($modal=='o') $modal=1; else $modal=0;
	$extension->Menus[$menu]=new Param_Menu($description, $pere, $action, $icon, $shortcut,$position,$modal,$help);
	$extension->IncrementBuild();
	return $xfer_result;
}

?>
 
