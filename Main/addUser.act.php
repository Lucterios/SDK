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

function addUser($Params)
{
	$xfer_result=new Xfer_Container_Custom("CORE","addUser",$Params);
	if (array_key_exists('user',$Params)) {
		$user=$Params['user'];
		$cnx=new Connect($user);
		$xfer_result->Caption='Modifier un utilisateur';
	}
	else {
		$cnx=new Connect('');
		$xfer_result->Caption='Ajouter un utilisateur';
	}

	require_once("Class/Extension.inc.php");
	$extlist=Extension::GetList();
	$extcore=new Extension("");
	$extlist["CORE"]=$extcore->GetVersion();
	$extcore=new Extension("applis");
	$extlist["applis"]=$extcore->GetVersion();

	$lbl=new Xfer_Comp_LabelForm('aliaslbl');
	$lbl->setValue("{[bold]}Alias{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('alias');
	$edt->setValue($cnx->Name);
	$edt->setLocation(1,0);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('LongNamelbl');
	$lbl->setValue("{[bold]}Nom long{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('LongName');
	$edt->setValue($cnx->LongName);
	$edt->setLocation(1,1);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('pass1Lbl');
	$lbl->setValue("{[bold]}Mot de passe{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	$pass=new Xfer_Comp_Passwd('pass1');
	$pass->setValue("");
	$pass->setLocation(1,2);
	$xfer_result->addComponent($pass);

	$lbl=new Xfer_Comp_LabelForm('pass2Lbl');
	$lbl->setValue("{[bold]}Re-mot de passe{[/bold]}");
	$lbl->setLocation(0,3);
	$xfer_result->addComponent($lbl);
	$pass=new Xfer_Comp_Passwd('pass2');
	$pass->setValue("");
	$pass->setLocation(1,3);
	$xfer_result->addComponent($pass);

	foreach($extlist as $ext_name=>$ext_version) {
		$select[$ext_name]=$ext_name;
		if ($cnx->Pwcrypt=="")
			$cnx->ReadOnly[]=$ext_name;
	}

	$lbl=new Xfer_Comp_LabelForm('NoViewLbl');
	$lbl->setValue("{[italic]}{[center]}Modules invisibles{[/center]}{[/italic]}");
	$lbl->setLocation(0,4);
	$xfer_result->addComponent($lbl);
	$chklst=new Xfer_Comp_CheckList('NoView');
	$chklst->setValue($cnx->NoView);
	$chklst->setSelect($select);
	$chklst->setLocation(0,5);
	$chklst->setSize(150,200);
	$xfer_result->addComponent($chklst);

	$lbl=new Xfer_Comp_LabelForm('ReadOnlyLbl');
	$lbl->setValue("{[italic]}{[center]}Modules modifiable{[/center]}{[/italic]}");
	$lbl->setLocation(1,4);
	$xfer_result->addComponent($lbl);
	$chklst=new Xfer_Comp_CheckList('Modified');
	$chklst->setValue($cnx->Modified);
	$chklst->setSelect($select);
	$chklst->setLocation(1,5);
	$chklst->setSize(150,200);
	$xfer_result->addComponent($chklst);
	
	$xfer_result->addAction(new Xfer_Action("_OK","ok.png","CORE","validUser"));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
