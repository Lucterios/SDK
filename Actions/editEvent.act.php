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

function editEvent($Params,$extensionname)
{
	$Params['type']='Event';
	require_once("Actions/editCode.act.php");
	$xfer_result=editCode($Params,$extensionname);
	$xfer_result->m_action='editEvent';
	require_once("Class/Event.inc.php");
	$event = new Event($Params['event'],$extensionname);
	$xfer_result->Caption="Edition de l'évenement \"".$event->GetName()."\"";

	$xfer_result->m_tab=0;
	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[bold]}{[center]}Evenement '".$event->GetName()."' de l'extension '$extensionname'{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,3);
	$xfer_result->addComponent($lbl);

	$xfer_result->removeComponents('code_name');
	$xfer_result->m_context['code_name']=$event->Mng->GetNameNoTable($event->Name);
	$edt=new Xfer_Comp_LabelForm('code_namelbl');
	$edt->setValue($event->Mng->GetNameNoTable($event->Name));
	$edt->setLocation(1,5);
	$xfer_result->addComponent($edt);

	$lbl=$xfer_result->getComponents('code_paramslbl');
	$lbl->setValue("function ".$event->ExtensionName.'::'.$event->GetName().'(&');

	return $xfer_result;
}

?>
 
