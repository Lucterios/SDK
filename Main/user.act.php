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

function user($Params)
{
	$xfer_result=&new Xfer_Container_Custom("CORE","user",$Params);
	$xfer_result->Caption='Les utilisateurs';

	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[bold]}{[center]}Les utilisateur de votre application{[/center]}{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);

	$grid=new Xfer_Comp_Grid('user');
	$grid->newHeader('A',"Alias",4);
	$grid->newHeader('B',"Nom",4);

	$mng= new ConnectManage();
	$users=$mng->GetList(true);
	foreach($users as $user)
	{
		$cnx=new Connect($user);
		$grid->setValue($user,'A',$user);
		$grid->setValue($user,'B',$cnx->LongName);
	}
	$grid->setLocation(0,1);
	$grid->addAction(new Xfer_Action("_Editer","","CORE","addUser",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Supprimer","","CORE","delUser",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","","CORE","addUser",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
	$xfer_result->addComponent($grid);
	
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
