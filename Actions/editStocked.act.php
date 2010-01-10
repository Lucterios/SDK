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

function editStocked($Params,$extensionname)
{
	$Params['type']='Stocked';
	require_once("Actions/editCode.act.php");
	$xfer_result=editCode($Params,$extensionname,false);
	$xfer_result->m_action='editStocked';
	require_once("Class/Stocked.inc.php");
	$stock = new Stocked($Params['stocked'],$extensionname);
	$xfer_result->Caption="Edition de la fonction stockée \"".$stock->GetName()."\"";

	$xfer_result->m_tab=0;
	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[bold]}{[center]}Fonction stockée '".$stock->GetName()."' de l'extension '$extensionname'{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,3);
	$xfer_result->addComponent($lbl);

	$xfer_result->m_tab=1;

	$lbl=$xfer_result->getComponents('code_paramslbl');
	$lbl->setValue("CREATE FUNCTION ".$stock->GetName().'(');

	$lbl=new Xfer_Comp_Label('code_paramslbl3');
	$lbl->setValue("RETURNS TEXT{[newline]}BEGIN{[newline]}DECLARE result TEXT DEFAULT '';");
	$lbl->setLocation(0,11,3);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Label('code_paramslbl4');
	$lbl->setValue("RETURN result;{[newline]}END");
	$lbl->setLocation(0,17,3);
	$xfer_result->addComponent($lbl);

	$xfer_result->addAction(new Xfer_Action("_ENVOYER EN DB","edit.png",$extensionname,"loadStocked",FORMTYPE_MODAL,CLOSE_NO));

	return $xfer_result;
}

?>
 
