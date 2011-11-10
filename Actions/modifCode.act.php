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

function modifCode($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"modifCode",$Params);
	if (array_key_exists('script_code',$Params)) {
		global $script_code;
		$script_code = urldecode($Params['script_code']);
	}
	if (array_key_exists('code_name',$Params)) {
		global $code_name;
		$code_name=$Params['code_name'];
	}
	if (array_key_exists('code_desc',$Params)) {
		global $code_desc;
		$code_desc=$Params['code_desc'];
	}
	if (array_key_exists('code_params',$Params)) {
		global $code_params;
		$code_params=$Params['code_params'];
	}
	if (array_key_exists('code_tableFiles',$Params)) {
		global $code_tableFiles;
		$code_tableFiles=explode(';',$Params['code_tableFiles']);
	}
	if (array_key_exists('code_index',$Params)) {
		global $code_index;
		$code_index=$Params['code_index'];
	}
	if (array_key_exists('classe',$Params)) {
		global $tablename;
		$tablename=$Params['classe'];
	}
	else	
		$tablename="";
	if (array_key_exists('Print_ModelDefault',$Params)) {
		global $Print_ModelDefault;
		$Print_ModelDefault=urldecode($Params['Print_ModelDefault']);
	}
	$type=$Params['type'];
	$id=$Params[strtolower($type)];

	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	require_once("Class/$type.inc.php");
	$extension=new Extension($extensionname);
	$code=new $type($id,$extension->Name,$tablename);
	if ($code->Modify($id,$tablename))
	{
		$code->Write();
		$extension->IncrementBuild();
	}
	return $xfer_result;
}

?>
 
