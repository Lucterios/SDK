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

function loadStocked($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"loadStocked",$Params);
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
	if (array_key_exists('classe',$Params)) {
		global $tablename;
		$tablename=$Params['classe'];
	}
	else	
		$tablename="";
	$id=$Params['stocked'];

	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	require_once("Class/Stocked.inc.php");
	$extension=new Extension($extensionname);
	$code=new Stocked($id,$extension->Name,$tablename);
	if ($code->Modify($id,$tablename))
	{
		$code->Write();
		$extension->IncrementBuild();
	}

	$fileName=$code->Mng->GetFileName($id,$extension->Name);
	
	$SQL="";
	$contents=File($fileName);
	foreach($contents as $line) {
		if ((substr($line,0,3)!='-- ') && (trim($line)!=''))
			$SQL.=rtrim($line)." ";
	}
	if ($SQL!='') {
		require_once('conf/cnf.inc.php');	
		require_once('CORE/dbcnx.inc.php');	
	
		if (class_exists('DBCNX')) {
			$connect = new DBCNX();
			$connect->connect($dbcnf);
			$connect->execute("DROP FUNCTION IF EXISTS ".$extension->Name."_FCT_".$code->GetName($code->Mng->SEP));
		
			if (!$connect->execute($SQL)) {
				$error=str_replace("\n","{[newline]}",$connect->errorMsg);
				$error=str_replace(array("{[newline]}{[newline]}"),"{[newline]}",$error);
				require_once("CORE/Lucterios_Error.inc.php");
				throw new LucteriosException(GRAVE,$error."{[newline]}{[newline]}");
			}
		}
		else {
			require_once("CORE/Lucterios_Error.inc.php");
			throw new LucteriosException(GRAVE,"Pas de connexion");
		}	
	}
	else {
		require_once("CORE/Lucterios_Error.inc.php");
		throw new LucteriosException(GRAVE,"Requette vide");
	}	

	return $xfer_result;
}

?>
 
