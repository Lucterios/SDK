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

function modifHelp($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"modifHelp",$Params);
	require_once("Class/Help.inc.php");
	$help_mng=new HelpManage($extensionname);
	$help_mng->HelpTitle=$Params['help_title'];
	$help_mng->HelpPosition=$Params['help_position'];
	if (($msg=$help_mng->writeHelp($extensionname))!=null)
		throw new Exception($msg);

	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	$extension->IncrementBuild();
	return $xfer_result;
}

?>
 
