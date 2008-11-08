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

function parameter($Params)
{
	$xfer_result=&new Xfer_Container_Custom("CORE","parameter",$Params);
	$xfer_result->Caption="Paramètrages";

	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setLocation(0,0,2);
	$lbl->setValue('{[center]}{[bold]}{[underline]}Paramètrages du SDK{[/underline]}{[/bold]}{[/center]}');
	$xfer_result->addComponent($lbl);

	//------------------------------------------------------
	$xfer_result->newTab('Déploiement');

	$conf_file=file("CNX/Server_Update.dt");
	$Project=trim($conf_file[0]);
	$Pass=trim($conf_file[1]);

	$lbl=new Xfer_Comp_LabelForm('depProjetLbl');
	$lbl->setLocation(0,0);
	$lbl->setValue('{[bold]}Projet{[/bold]}');
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Edit('depProjet');
	$lbl->setLocation(1,0);
	$lbl->setValue($Project);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('depPassLbl');
	$lbl->setLocation(2,0);
	$lbl->setValue('{[bold]}Mot de passe{[/bold]}');
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Edit('depPass');
	$lbl->setLocation(3,0);
	$lbl->setValue($Pass);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('depServerLbl');
	$lbl->setLocation(0,1);
	$lbl->setValue('{[bold]}Serveurs{[/bold]}');
	$xfer_result->addComponent($lbl);

	$grid=new Xfer_Comp_Grid('depServer');
	$grid->addHeader('server','Serveur');
	for($i=2;$i<count($conf_file);$i++)
		$grid->setValue($i, 'server',trim($conf_file[$i]));
	$grid->setLocation(1,1,3);
	$grid->addAction(new Xfer_Action("_Ajouter","add.png","CORE","paramAddServer",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
	$grid->addAction(new Xfer_Action("_Supprimer","suppr.png","CORE","paramDelServer",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$xfer_result->addComponent($grid);

	//------------------------------------------------------
	$xfer_result->newTab('Tests unitaires');

	$lbl=new Xfer_Comp_LabelForm('testTitleLbl');
	$lbl->setLocation(0,0,2);
	$lbl->setValue('{[bold]}{[center]}Connexion à la Base de Données{[/center]}{[/bold]}');
	$xfer_result->addComponent($lbl);

	$DBUnitTest=file("CNX/DBUnitTest.dt");
	$dbuser=trim($DBUnitTest[0]);
	$dbpass=trim($DBUnitTest[1]);
	$dbname=trim($DBUnitTest[2]);

	$lbl=new Xfer_Comp_LabelForm('testUserLbl');
	$lbl->setLocation(0,1);
	$lbl->setValue('{[bold]}Utilisateur{[/bold]}');
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Edit('testUser');
	$lbl->setLocation(1,1);
	$lbl->setValue($dbuser);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('TestPassLbl');
	$lbl->setLocation(0,2);
	$lbl->setValue('{[bold]}Mot de passe{[/bold]}');
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Edit('testPass');
	$lbl->setLocation(1,2);
	$lbl->setValue($dbpass);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('TestBaseLbl');
	$lbl->setLocation(0,3);
	$lbl->setValue('{[bold]}Nom de base de données{[/bold]}');
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Edit('testBase');
	$lbl->setLocation(1,3);
	$lbl->setValue($dbname);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Button('testBtb');
	$lbl->setLocation(0,4,2);
	$lbl->setAction(new Xfer_Action("_Modifier","","CORE","paramModifTest",FORMTYPE_MODAL,CLOSE_NO));
	$xfer_result->addComponent($lbl);

	//------------------------------------------------------
	$xfer_result->newTab('Licence');

	$lbl=new Xfer_Comp_Button('testBtb');
	$lbl->setLocation(0,0);
	$lbl->setAction(new Xfer_Action("_Modifier","","CORE","paramModifLicence",FORMTYPE_MODAL,CLOSE_NO));
	$xfer_result->addComponent($lbl);

	$license_lines = file('CNX/LICENSE');
	$license_text = implode('{[newline]}',$license_lines);
	$lbl=new Xfer_Comp_LabelForm('LicenceLbl');
	$lbl->setLocation(0,1);
	$lbl->setValue("{[italic]}$license_text{[/italic]}");
	$xfer_result->addComponent($lbl);

	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
