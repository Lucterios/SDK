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

function paramAddServer($Params)
{
	if (array_key_exists('new_server',$Params)) {
		$xfer_result=&new Xfer_Container_Acknowledge("CORE","paramAddServer",$Params);
		$xfer_result->Caption="Ajout de serveur";
	
		$Project=trim($Params['depProjet']);
		$Pass=trim($Params['depPass']);
		$new_server=trim($Params['new_server']);
	
		$conf_file=file("CNX/Server_Update.dt");
		$conf_file[0]=$Project;
		$conf_file[1]=$Pass;
		$conf_file[]=$new_server;

		if ($fh=fopen("CNX/Server_Update.dt","w+"))
		{
			for($i=0;$i<count($conf_file);$i++) {
				$conf_line=trim($conf_file[$i]);
				if (($i<2) || ($conf_line!=''))
					fwrite($fh,"$conf_line\n"); 
			}
			fclose($fh);
		}
		
	}
	else {
		$xfer_result=&new Xfer_Container_Custom("CORE","paramAddServer",$Params);
		$xfer_result->Caption="Ajout de serveur";

		$lbl=new Xfer_Comp_LabelForm('new_serverLbl');
		$lbl->setLocation(0,0);
		$lbl->setValue('{[bold]}Nouveau serveur{[/bold]}');
		$xfer_result->addComponent($lbl);
	
		$lbl=new Xfer_Comp_Edit('new_server');
		$lbl->setLocation(1,0);
		$lbl->setValue();
		$xfer_result->addComponent($lbl);
	
		$xfer_result->addAction(new Xfer_Action("_Valider","ok.png","CORE","paramAddServer"));
		$xfer_result->addAction(new Xfer_Action("_Annuler","cancel.png"));
	}
	return $xfer_result;
}

?>
 
