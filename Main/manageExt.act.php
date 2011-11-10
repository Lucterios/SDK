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
require_once('Main/connection.inc.php');

function manageExt($Params)
{
	$xfer_result=&new Xfer_Container_Custom("CORE","manageExt",$Params);
	$xfer_result->Caption="Gestion d'une extension";

	$ext=$Params['ext'];
	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	require_once("Class/Extension.inc.php");
	$lock=Extension::GetLock($ext);

	$lbl=new Xfer_Comp_LabelForm('title1');
	$lbl->setValue("{[bold]}{[center]}$ext{[/center]}{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('title2');
	$lbl->setValue("{[bold]}{[center]}$lock{[/center]}{[/bold]}");
	$lbl->setLocation(1,0);
	$xfer_result->addComponent($lbl);

	$grid=new Xfer_Comp_Grid('arch');
	$grid->newHeader('A',"Version",4);
	$grid->newHeader('B',"Développeur",4);
	$grid->newHeader('C',"Date",4);

	$arch=Extension::BackupFiles($ext);
	foreach($arch as $key=>$val)
	{
		$grid->setValue($key,'A',$val[0]);
		$grid->setValue($key,'B',$val[1]);
		$grid->setValue($key,'C',$val[2]);
	}
	$grid->setLocation(0,1,2);
	$grid->setSize(400,600);
	$xfer_result->addComponent($grid);

	$lock_user=explode("-",$lock);
	$lock_user=trim($lock_user[0]);
	if ($lock=="")
	{
		$extObj=new Extension($ext);
		if (($extObj->SignBy=='') && ($extObj->SignMD5==''))
		{
			if (!in_array($ext,array("CORE","applis")))
				$xfer_result->addAction(new Xfer_Action("_Suprimer","","CORE","delExt",FORMTYPE_MODAL,CLOSE_YES));
			$xfer_result->addAction(new Xfer_Action("_Réserver","","CORE","modifLock",FORMTYPE_MODAL,CLOSE_YES));
		}
	}
	else if ($lock_user==$cnx->Name)
	{
		$xfer_result->addAction(new Xfer_Action("_Valider la réservation","","CORE","modifLock",FORMTYPE_MODAL,CLOSE_YES));
		$bachup_file=Extension::GetBackupFile($ext,$lock);
		if (is_file($bachup_file))
			$xfer_result->addAction(new Xfer_Action("_Annuler la réservation","","CORE","cancelLock",FORMTYPE_MODAL,CLOSE_YES));	
	}
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
 
