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

function paramAddRepo($Params)
{
	if (array_key_exists('new_repo',$Params)) {
		$xfer_result=&new Xfer_Container_Acknowledge("CORE","paramAddRepo",$Params);
		$xfer_result->Caption="Ajout de repository";
	
		$new_server=trim($Params['new_repo']);
		if ($new_server[-1]!='/')
		  $new_server.='/';
		  
		/*if (substr($new_server,0,6)=='ssh://') {
		    require_once("Class/Git.php");
		    $cmd = $new_server;
		    $cmd = str_replace('://',' ',$cmd);
		    $pos = strpos($cmd, '/');
		    if ($pos!==false)
		      $cmd = substr($cmd,0,$pos); 
		    list($out,$ret) = my_exec($cmd,array('yes'));
		    print "<!-- paramAddRepo SSH cm='$cmd' ret=$ret out=".print_r($out,True)." -->\n";
		}*/
	
		$conf_file=file("CNX/Conf_Manage.dt");
		$conf_file[0]=$conf_file[0];
		$conf_file[1]=$conf_file[1];
		$conf_file[]=$new_server;

		if ($fh=fopen("CNX/Conf_Manage.dt","w+"))
		{
			for($i=0;$i<count($conf_file);$i++) {
				$conf_line=trim($conf_file[$i]);
				if (($i<2) || ($conf_line!=''))
					fwrite($fh,"$conf_line\n"); 
			}
			fclose($fh);
			chmod("CNX/Conf_Manage.dt", 0666);
		}
		
	}
	else {
		$xfer_result=&new Xfer_Container_Custom("CORE","paramAddRepo",$Params);
		$xfer_result->Caption="Ajout de repository";

		$lbl=new Xfer_Comp_LabelForm('new_repoLbl');
		$lbl->setLocation(0,0);
		$lbl->setValue('{[bold]}Nouveau repository{[/bold]}');
		$xfer_result->addComponent($lbl);
	
		$lbl=new Xfer_Comp_Edit('new_repo');
		$lbl->setLocation(1,0);
		$lbl->setValue();
		$xfer_result->addComponent($lbl);
	
		$xfer_result->addAction(new Xfer_Action("_Valider","ok.png","CORE","paramAddRepo"));
		$xfer_result->addAction(new Xfer_Action("_Annuler","cancel.png"));
	}
	return $xfer_result;
}

?>
 
