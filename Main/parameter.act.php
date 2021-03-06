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
	require_once('Class/Git.php');
	$xfer_result=new Xfer_Container_Custom("CORE","parameter",$Params);
	$xfer_result->Caption="Param�trages";

	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setLocation(0,0,2);
	$lbl->setValue('{[center]}{[bold]}{[underline]}Param�trages du SDK{[/underline]}{[/bold]}{[/center]}');
	$xfer_result->addComponent($lbl);

	//------------------------------------------------------
	$xfer_result->newTab('Gestion de configuration');
	$conf_file=file("CNX/Conf_Manage.dt");
	$gitUser=$conf_file[0];
	$gitEmail=$conf_file[1];

	$lbl=new Xfer_Comp_LabelForm('sshkeyLbl');
	$lbl->setLocation(0,1);
	$lbl->setValue('{[bold]}Clef SSH{[/bold]}');
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Memo('sshkey');
	$lbl->setLocation(1,1,4);
	$lbl->setSize(10,10);
	$lbl->setValue(get_ssh_key());	
	$xfer_result->addComponent($lbl);  

	$lbl=new Xfer_Comp_LabelForm('gitUserLbl');
	$lbl->setLocation(0,2);
	$lbl->setValue('{[bold]}user.name{[/bold]}');
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Edit('gitUser');
	$lbl->setLocation(1,2);
	$lbl->setValue($gitUser);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('gitEmailLbl');
	$lbl->setLocation(0,3);
	$lbl->setValue('{[bold]}user.email{[/bold]}');
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Edit('gitEmail');
	$lbl->setLocation(1,3);
	$lbl->setValue($gitEmail);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Button('gitBtn');
	$lbl->setLocation(2,2,2,2);
	$lbl->setAction(new Xfer_Action("","ok.png","CORE","paramUserGit",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('depRepositoryLbl');
	$lbl->setLocation(0,4);
	$lbl->setValue('{[bold]}Repositories{[/bold]}');
	$xfer_result->addComponent($lbl);

	$grid=new Xfer_Comp_Grid('depRepository');
	$grid->addHeader('repo','Repository');
	for($i=2;$i<count($conf_file);$i++)
		$grid->setValue($i, 'repo',trim($conf_file[$i]));
	$grid->setSize(300,10);
	$grid->setLocation(1,4,3);
	$grid->addAction(new Xfer_Action("_Ajouter","add.png","CORE","paramAddRepo",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
	$grid->addAction(new Xfer_Action("_Supprimer","suppr.png","CORE","paramDelRepo",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));

	$errorGit=array();
	if ($gitUser=='')
	  $errorGit[]='La config GIT "user.name" est vide!';
	if ($gitEmail=='')
	  $errorGit[]='La config GIT "user.email" est vide!';

	$lbl=new Xfer_Comp_LabelForm('gitConfErrorLbl');
	$lbl->setLocation(0,5,4);
	$lbl->setValue("{[center]}{[bold]}{[font color='red']}".implode('{[newline]}',$errorGit)."{[/font]}{[/bold]}{[/center]}");
	$xfer_result->addComponent($lbl);
	
	$xfer_result->addComponent($grid);

	//------------------------------------------------------
	$xfer_result->newTab('Tests unitaires');

	$lbl=new Xfer_Comp_LabelForm('testTitleLbl');
	$lbl->setLocation(0,0,2);
	$lbl->setValue('{[bold]}{[center]}Connexion � la Base de Donn�es{[/center]}{[/bold]}');
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
	$lbl->setValue('{[bold]}Nom de base de donn�es{[/bold]}');
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
 
