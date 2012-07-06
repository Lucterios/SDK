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

function incVersion($Params)
{
	require_once('../CORE/xfer.inc.php');
	$xfer_result=&new Xfer_Container_Acknowledge("CORE","incVersion",$Params);
	$increm_version=$Params['IncVersion'];
	$ext=$Params['ext'];
	require_once("Class/Extension.inc.php");
	$ext_obj=new Extension($ext);

	switch($increm_version) {
		case 0: //non
			break;
		case 1: //Révision
			$ext_obj->IncrementRelease();
			break;
		case 2: //Sous-version
			$ext_obj->IncrementSubVersion();
			break;
		case 3: //Version
			$ext_obj->IncrementVersion();
			break;
	}
	return $xfer_result;
} 
