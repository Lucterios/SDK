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

function addExt($Params)
{
	$xfer_result=new Xfer_Container_Custom("CORE","addExt",$Params);
	$xfer_result->Caption='Ajouter une extension';

	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[bold]}{[center]}Nom de ma nouvelle extension{[/center]}{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);

	$edt=new Xfer_Comp_Edit('newExt');
	$edt->setValue("");
	$edt->ExprReg="[a-zA-Z][a-zA-Z_0-9]*";
	$edt->StringSize=100;
	$edt->setLocation(1,0);
	$xfer_result->addComponent($edt);
	
	$xfer_result->addAction(new Xfer_Action("_OK","ok.png","CORE","addExtValid"));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
