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
require_once('FunctionTool.inc.php');

function addFieldValid($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"addFieldValid",$Params);
	$classe=$Params['classe'];
	require_once("Class/Extension.inc.php");
	require_once("Class/Table.inc.php");
	$extension=new Extension($extensionname);
	$table=new Table($classe,$extensionname);
	if (array_key_exists('name',$Params)) {
		$field_name=$Params['name'];
		$field=$table->Fields[$field_name];
	} else {
		$field_name='';
		$field=array('description'=>'','type'=>0,'notnull'=>false,'params'=>array());
	}


	$field_type=(int)$Params['field_type'];
	switch($field_type)
	{
		case "0": //int
			$param=array("Min"=>(int)$Params['field_ValMin'], "Max"=>(int)$Params['field_ValMax']);
			break;
		case "1": //real
			$param=array("Min"=>(float)$Params['field_ValMin'], "Max"=>(float)$Params['field_ValMax'], "Prec"=>(int)$Params['field_ValPrec']);
			break;
		case "2": //string
			$param=array("Size"=>(int)$Params['field_ValSize'], "Multi"=>($Params['field_ValMulti']=='o'));
			break;
		case "8": //enum
			$param=array("Enum"=>split(";",trim($Params['field_ValEnum'])));
			break;
		case "9": //childs
			$param=array("TableName"=>$Params['field_TableName'], "RefField"=>$Params['field_RefField']);
			break;
		case "10": //reference
			$param=array("TableName"=>$Params['field_TableName']);
			break;
		case "11": //function
			$field_Function=$Params['field_Function'];
			$pos=strpos($field_Function,"_FCT_");
			require_once("Class/Stocked.inc.php");
			$code=new Stocked(substr($field_Function,$pos+5),$extensionname);
			$param=array("Function"=>$field_Function,"NbField"=>count($code->Parameters));
			break;
		case "12": //method chaine
			$param=array("MethodGet"=>$Params['field_MethodGet'],"MethodSet"=>$Params['field_MethodSet']);
			break;
		case "13": //method réel
			$param=array("MethodGet"=>$Params['field_MethodGet'],"MethodSet"=>$Params['field_MethodSet'],"Min"=>(float)$Params['field_ValMin'], "Max"=>(float)$Params['field_ValMax'], "Prec"=>(int)$Params['field_ValPrec']);
			break;
		default:
			$param=array();
			break;
	}
	$table->ModifyField($field_name,$Params['field_description'],$field_type,$Params['field_notnull']=='o',$param);
	$extension->IncrementBuild();

	return $xfer_result;
}

?>
