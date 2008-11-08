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

function paramModifLicence($Params)
{
	if (array_key_exists('new_licence',$Params)) {
		$xfer_result=&new Xfer_Container_Acknowledge("CORE","paramModifLicence",$Params);
		$xfer_result->Caption="Modifier la licence";
	
		$new_licence=trim($Params['new_licence']);	
		$new_licence_file=split('{[newline]}',$new_licence);

		if ($fh=fopen("CNX/LICENSE","w+"))
		{
			for($i=0;$i<count($new_licence_file);$i++) {
				$conf_line=trim($new_licence_file[$i]);
				if ($conf_line!='')
					fwrite($fh,"$conf_line\n"); 
			}
			fclose($fh);
		}
		
	}
	else {
		$xfer_result=&new Xfer_Container_Custom("CORE","paramModifLicence",$Params);
		$xfer_result->Caption="Modifier la licence";

		$license_lines = file('CNX/LICENSE');
		$license_text = implode('{[newline]}',$license_lines);
		$lbl=new Xfer_Comp_Memo('new_licence');
		$lbl->setLocation(0,1);
		$lbl->setSize(350,600);
		$lbl->setValue($license_text);
		$xfer_result->addComponent($lbl);
	
		$xfer_result->addAction(new Xfer_Action("_Valider","ok.png","CORE","paramModifLicence"));
		$xfer_result->addAction(new Xfer_Action("_Annuler","cancel.png"));
	}
	return $xfer_result;
}

?>
 
