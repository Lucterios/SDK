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

function addSignal($Params,$extensionname)
{
	global $CNX_OBJ;
	$cnx=$CNX_OBJ;

	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);

	$xfer_result=&new Xfer_Container_Custom($extensionname,"addSignal",$Params);
	if (isset($Params['signal'])) {
	    $signalId=$Params['signal'];
	    $signalData=$extension->Signals[$signalId];
	    $xfer_result->Caption="Edition d'un signal";
	}
	else {
	    $signalId=-1;
	    $signalData=array("","&\$xfer_result,\$DBObj","");
	    $xfer_result->Caption="Nouveau signal";
	}

	$lbl=new Xfer_Comp_LabelForm('SignalIdentifiantlbl');
	$lbl->setValue("{[bold]}Identifiant{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
if ($signalId==-1) {
	$edt=new Xfer_Comp_Edit('SignalIdentifiant');
	$edt->setLocation(1,0);
	$edt->setNeeded(true);
	$edt->ExprReg="[a-zA-Z][a-zA-Z0-9_]*";
	$edt->StringSize=100;
	$xfer_result->addComponent($edt);
}else{
	$xfer_result->m_context['SignalIdentifiant']=$signalData[0];
	$lbl=new Xfer_Comp_Label('SignalIdentifiant');
	$lbl->setValue($signalData[0]);
	$lbl->setLocation(1,0);
	$xfer_result->addComponent($lbl);
}

	$lbl=new Xfer_Comp_LabelForm('SignalParamslbl');
	$lbl->setValue("{[bold]}Parameters{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('SignalParams');
	$edt->setValue($signalData[1]);
	$edt->setLocation(1,1);
	$edt->ExprReg="\\&(\\\$|\\\$[a-zA-Z]|\\\$[a-zA-Z][a-zA-Z0-9_]*)(,|,\\\$|\\\$[a-zA-Z]|,\\\$[a-zA-Z][a-zA-Z0-9_]*)*";
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('SignalDescriptionlbl');
	$lbl->setValue("{[bold]}Description{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('SignalDescription');
	$edt->setValue($signalData[2]);
	$edt->setNeeded(true);
	$edt->setLocation(1,2);
	$xfer_result->addComponent($edt);


	if (!$cnx->IsReadOnly($extensionname))
		$xfer_result->addAction(new Xfer_Action("_OK","ok.png",$extensionname,"addSignalValid",FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));

	return $xfer_result;
}

?>
 
