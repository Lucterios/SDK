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

function deleteClasse($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"deleteClasse",$Params);
	$classe=$Params['classe'];
	if ($xfer_result->Confirme("Etes-vous sûre de vouloir supprimer la classe '$classe'?")) {
		require_once("Class/Extension.inc.php");
		require_once("Class/Table.inc.php");
		require_once("Class/Action.inc.php");
		$extension=new Extension($extensionname);
		$mng= new TableManage();
		$mng->delete($classe,$extension->Name);
		$mngact= new ActionManage();
		foreach($extension->Actions as $key=>$act)
		if ($mngact->GetTableName($act->action)==$id)
			unset($extension->Actions[$key]);
		$extension->IncrementRelease();
	}
	return $xfer_result;
}

?>
 
