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

function generatorTest($Params,$extensionname)
{
	$xfer_result=new Xfer_Container_Custom($extensionname,"generatorTest",$Params);
	$lbl=new Xfer_Comp_LabelForm('generatortitle');
	$lbl->setValue("{[bold]}{[center]}Génération activée{[/center]}{[/bold]}{[newline]}Veuillez simuler votre test sur votre serveur de débug.");
	$lbl->setLocation(0,5);
	$xfer_result->addComponent($lbl);
	
	require_once('Class/Config.inc.php');
	$conf=new Config('conf.db');
	$conf->xmlSaving='tmp/saving.xml';
	$conf->Write();
	$xfer_result->m_context['xmlSaving']='../'.$conf->xmlSaving;
	unlink($xfer_result->m_context['xmlSaving']);
	if ($fh=fopen($xfer_result->m_context['xmlSaving'],"w+"))
	{
		fwrite($fh,"<?xml version='1.0' encoding='iso-8859-1'?>\n");
		fwrite($fh,"<SAVE>\n");
		fwrite($fh,"</SAVE>\n");
		fclose($fh);
	}
	
	$xfer_result->setCloseAction(new Xfer_Action("","",$extensionname,"generatorTestClose"));
	$xfer_result->addAction(new Xfer_Action("_Générer","ok.png",$extensionname,"generatorTestValid",FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Annuler","cancel.png"));
	
	return $xfer_result;
}

?>
 
 
