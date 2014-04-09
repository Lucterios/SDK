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

function addInitialValid($Params,$extensionname)
{
	$xfer_result=new Xfer_Container_Acknowledge($extensionname,"addInitialValid",$Params);
	$classe=$Params['classe'];
	require_once("Class/Extension.inc.php");
	require_once("Class/Table.inc.php");
	$extension=new Extension($extensionname);
	$table=new Table($classe,$extensionname);
	if (array_key_exists('initial',$Params)) {
		$initial_id=$Params['initial'];
		$initial=$table->DefaultFields[$initial_id];
	} else {
		$initial_id=-1;
		$initial=array('@refresh@'=>true,'id'=>'');
	}
	$initial['@refresh@']=($Params['refreshïdata']=='o');
	if ($Params['id']>0)
		$initial['id']=$Params['id'];
	else
		$initial['id']='';
	foreach($table->Fields as $key=>$field) {
		$type_id=(int)$field['type'];
		if ($type_id!=9) {
			if (array_key_exists($key,$Params)) 
				$initial[$key]=$Params[$key];
			else
				$initial[$key]='';
		}
		else
			unset($initial[$key]);
	}

	if ($initial_id!=-1)
		$table->DefaultFields[$initial_id]=$initial;
	else
		$table->DefaultFields[]=$initial;
	$table->Write();
	$extension->IncrementBuild();

	return $xfer_result;
}

?>
