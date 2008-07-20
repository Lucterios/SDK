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

function modifAction($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"modifAction",$Params);
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
		$code_tableFiles=split(';',$Params['code_tableFiles']);
	}
	if (array_key_exists('code_index',$Params)) {
		global $code_index;
		$code_index=$Params['code_index'];
	}
	if (array_key_exists('action_XferCnt',$Params)) {
		global $action_XferCnt;
		$action_XferCnt=$Params['action_XferCnt'];
	}
	if (array_key_exists('action_Lock',$Params)) {
		global $action_Lock;
		$action_Lock=$Params['action_Lock'];
	}
	if (array_key_exists('action_Transaction',$Params) && ($Params['action_Transaction']!='n')) {
		global $action_Transaction;
		$action_Transaction=$Params['action_Transaction'];
	}
	if (array_key_exists('action_droit',$Params)) {
		global $action_droit;
		$action_droit=$Params['action_droit'];
	}
	if (array_key_exists('classe',$Params)) {
		global $tablename;
		$tablename=$Params['classe'];
	}
	$id=$Params['action'];
	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	require_once("Class/Action.inc.php");
	$extension=new Extension($extensionname);
	$code=new Action($id,$extension->Name,$tablename);
	if ($code->Modify($id,$tablename))
	{
		$code->Write();

		if (isset($tablename) && (strpos($code_name,SEP)===false))
			$code_name=$tablename.SEP.$code_name;

		$action_id=count($extension->Actions);
		foreach($extension->Actions as $id_act=>$current_act)
			if ($current_act->action==$code_name)
				$action_id=$id_act;
		$extension->Actions[$action_id]=new Param_Action($code_desc, $code_name, $action_droit);
		$extension->IncrementBuild();
	}
	return $xfer_result;
}

?>
 
