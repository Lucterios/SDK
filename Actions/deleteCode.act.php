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

function deleteCode($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"deleteCode",$Params);
	$type=$Params['type'];
	$id=$Params[strtolower($type)];
	if (array_keys('classe',$Params))
		$tablename=$Params['classe'];
	else	
		$tablename="";
	require_once("Class/$type.inc.php");

	$ManageClass=$type."Manage";
	$mng= new $ManageClass();
	$tablename=$mng->GetTableName($id);
	$mng->delete($id,$extensionname);
	return $xfer_result;
}

?>
 
