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

require_once('../CORE/xfer.inc.php');
require_once('../CORE/xfer_custom.inc.php');

function addClasse($Params,$extensionname)
{
	if (array_key_exists('new_name',$Params)) {
		$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"addClasse",$Params);
		require_once("Class/Extension.inc.php");
		require_once("Class/Table.inc.php");
		$extension=new Extension($extensionname);
		$tbl=new Table($Params['new_name'],$extension->Name);
		if (array_key_exists('field_type',$Params) && array_key_exists('classe',$Params) && array_key_exists('new_subfield',$Params) && ($Params['field_type']==9))
		{
			$param=array("TableName"=>$extensionname.'_'.$Params['classe'],"CascadeMerge"=>true);
			$tbl->ModifyField($Params['new_subfield'],'Reference a '.$Params['classe'], 10, true, $param);
		}
		$tbl->Write();
		$extension->IncrementBuild();
	} else {
		$xfer_result=&new Xfer_Container_Custom($extensionname,"addClasse",$Params);
		$lbl=new Xfer_Comp_LabelForm('new_namelbl');
		$lbl->setValue("{[bold]}{[center]}Nom{[/center]}{[/bold]}");
		$lbl->setLocation(0,0);
		$xfer_result->addComponent($lbl);
	
		$edt=new Xfer_Comp_Edit('new_name');
		$edt->setValue("");
		$edt->ExprReg="[a-zA-Z][a-zA-Z0-9]*";
		$edt->StringSize=100;
		$edt->setLocation(1,0);
		$xfer_result->addComponent($edt);

		if (array_key_exists('field_type',$Params) && array_key_exists('classe',$Params) && ($Params['field_type']==9)) {
			$lbl=new Xfer_Comp_LabelForm('new_subfieldlbl');
			$lbl->setValue("{[bold]}{[center]}Nom du champ lier {[newline]} a la classe '".$Params['classe']."'{[/center]}{[/bold]}");
			$lbl->setLocation(0,1);
			$xfer_result->addComponent($lbl);
	
			$edt=new Xfer_Comp_Edit('new_subfield');
			$edt->setValue(strtolower($Params['classe'])."Id");
			$edt->ExprReg="[a-zA-Z][a-zA-Z0-9]*";
			$edt->StringSize=100;
			$edt->setLocation(1,1);
			$xfer_result->addComponent($edt);
		}

		$xfer_result->addAction(new Xfer_Action("_OK","ok.png",$extensionname,"addClasse"));
		$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	}
	return $xfer_result;
}

?>
 
