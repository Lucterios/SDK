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

function visuLog($Params)
{
	$xfer_result=&new Xfer_Container_Custom("CORE","visuLog",$Params);
	$xfer_result->Caption="Visualisation des logs";
	
	$log_file="../tmp/LuceriosCORE.log";
	$log_content_lines=array_reverse(file($log_file));
	$current_nb=0;
	$log_content="";		
	foreach($log_content_lines as $current_line) {
		if (trim($current_line)!='') {
			$current_line=str_replace(array('<','>',' ',"\t"),array('&lt;','&gt;','&#160;','&#160;&#160;&#160;'),$current_line);
			$log_content.=$current_line.'{[newline]}';
			$current_nb++;
		}
		if ($current_nb>500)
			break;
	}
	
	$xfer_result->newTab('Journal de débug');
	
	$lbl=new Xfer_Comp_LabelForm('LogLbl');
	$lbl->setLocation(0,1,2);
	$lbl->setValue("{[italic]}$log_content{[/italic]}");
	$xfer_result->addComponent($lbl);

	$xfer_result->newTab('Paramètres');

	require_once('Class/Config.inc.php');
	$conf=new Config('conf.db');
	if (isset($Params['isActif'])) {
		$isActif=($Params['isActif']=='o');
		if ($conf->debugMode!=$isActif) {
			$conf->debugMode=$isActif;
			$conf->Write();
		}
	}

	$lbl=new  Xfer_Comp_LabelForm("isActiflbl");
	$lbl->setValue("{[bold]}Débug actif?{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$edt=new  Xfer_Comp_Check("isActif");
	$edt->setValue($conf->debugMode);
	$edt->setLocation(1,1);
	$edt->setAction(new Xfer_Action("","","CORE","visuLog",FORMTYPE_REFRESH,CLOSE_NO));
	$xfer_result->addComponent($edt);


	$xfer_result->addAction(new Xfer_Action("_Vider","delete.png","CORE","deleteLog",FORMTYPE_MODAL,CLOSE_NO));
	$xfer_result->addAction(new Xfer_Action("_Rafraichir","refresh.png","CORE","visuLog",FORMTYPE_REFRESH,CLOSE_NO));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
