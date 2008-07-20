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

function addParamValid($Params,$extensionname)
{
	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);

	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"addParamValid",$Params);
	$param_name=$Params['name'];
	$param_value=$Params['valeur'];
	$param_description=$Params['description'];
	$param_type=$Params['type'];
	$param_ValMin=$Params['ValMin'];
	$param_ValMax=$Params['ValMax'];
	$param_ValPrec=$Params['ValPrec'];
	$param_ValEnum=$Params['ValEnum'];
        $param_ValMulti=$Params['ValMulti'];
	$extend=array();
	switch($param_type)
	{
		case PARAM_TYPE_STR:
			$extend['Multi']=$param_ValMulti!='n';
			break;
		case PARAM_TYPE_INT:
			$extend['Min']=(int)$param_ValMin;
			$extend['Max']=(int)$param_ValMax;
			break;
		case PARAM_TYPE_REAL:
			$extend['Min']=(float)$param_ValMin;
			$extend['Max']=(float)$param_ValMax;
			$extend['Prec']=(int)$param_ValPrec;
			break;
		case PARAM_TYPE_BOOL:
			if ($param_value!='n')
				$param_value='o';
			else
				$param_value='n';
			break;
		case PARAM_TYPE_ENUM:
			$extend['Enum']=split(";",trim($param_ValEnum));
			break;
	}
	$new_param=new Param_Parameters($param_name,$param_value,$param_description,$param_type,$extend);
	$extension->Params[$param_name]=$new_param;
	$extension->IncrementBuild();
	return $xfer_result;
}

?>
 
