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

function sourceExtension($Params)
{
	$xfer_result=new Xfer_Container_Custom("CORE","sourceExtension",$Params);
	$xfer_result->Caption="Gestion de configuration";

	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[bold]}{[center]}Les extensions de votre application{[/center]}{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);

	$grid=new Xfer_Comp_Grid('ext');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Version",4);
	$grid->newHeader('C',"Info GIT",4);

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;

	require_once("Class/Extension.inc.php");
	$mods=Extension::GetList($cnx,false);
	foreach($mods as $mod_name=>$mod_ext)
	{
		if ($cnx->CanWriteModule($mod_name)) {
			$grid->setValue($mod_name,'A',$mod_name);
			$grid->setValue($mod_name,'B',$mod_ext->GetVersion());
			$grid->setValue($mod_name,'C',$mod_ext->GetInfoGit());
		}
	}
	$grid->setLocation(0,1);
	$grid->addAction(new Xfer_Action("_Editer","edit.png","CORE","manageSourceExt",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Supprimer","suppr.png","CORE","delExt",FORMTYPE_MODAL,CLOSE_YES,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","add.png","CORE","addExt",FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
	$xfer_result->addComponent($grid);
	
	$xfer_result->addAction(new Xfer_Action("'_Pull' tout","refresh.png","CORE","pullAllGitExt",FORMTYPE_MODAL,CLOSE_NO));
	$xfer_result->addAction(new Xfer_Action("_Rafraichir DB","refresh.png","CORE","refreshExts",FORMTYPE_MODAL,CLOSE_NO));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
 
