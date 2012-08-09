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

function manageSourceExt($Params)
{
	$xfer_result=&new Xfer_Container_Custom("CORE","manageSourceExt",$Params);
	$xfer_result->Caption="Gestion des source d'une extension";

	$ext=$Params['ext'];
	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	require_once("Class/Extension.inc.php");
	$extObj=new Extension($ext);

	$lbl=new Xfer_Comp_LabelForm('title1');
	$lbl->setValue("{[center]}{[bold]}$extObj->Titre{[/bold]} ($ext) {[italic]}".$extObj->GetVersion()."{[/italic]}{[/center]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);

	if (substr($git_info,0,2)!='**') {
		global $GIT_STATUS_TAB;
		$repo=$extObj->GetGitRepoObj();
		$status=$repo->getStatus();

		$lbl=new Xfer_Comp_LabelForm('lblgitlog');
		$lbl->setValue("{[bold]}{[center]}Les 10 derniers commit{[/center]}{[/bold]}");
		$lbl->setLocation(0,1);
		$xfer_result->addComponent($lbl);

		$msg=$repo->run('log -n 10 --pretty=format:"%ar - {[font color=blue]}%an{[/font]} : {[font color=green]}%s{[/font]} {[font color=red]}%d{[/font]}"');
		$lbl=new Xfer_Comp_LabelForm('gitlog');
		$lbl->setValue(str_replace("\n","{[newline]}",$msg));
		$lbl->setLocation(1,1,3);
		$xfer_result->addComponent($lbl);

		$modify=false;
		$grid=new Xfer_Comp_Grid('files');
		$grid->newHeader('A',"Nom",4);
		$grid->newHeader('B',"Etat Git",4);
		foreach($status as $file_name=>$status_val) {
			if ($status_val!='??') $modify=true;
			$grid->setValue($file_name,'A',$file_name);
			if (isset($GIT_STATUS_TAB[$status_val]))
			    $grid->setValue($file_name,'B',$GIT_STATUS_TAB[$status_val]);
			else
			    $grid->setValue($file_name,'B',"[$status_val]");
		}
		$grid->setLocation(0,2,4);
		$grid->setSize(500,500);
		$grid->addAction(new Xfer_Action("Diff","","CORE","diffFileGit",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
		$xfer_result->addComponent($grid);

		$lbl=new Xfer_Comp_LabelForm('inclbl');
		$lbl->setValue("{[bold]}Incrémenter la version{[/bold]}");
		$lbl->setLocation(1,3);
		$xfer_result->addComponent($lbl);

		$chk=new Xfer_Comp_Select('IncVersion');
		$chk->setSelect(array(0=>'<non>',1=>"Révision",2=>"Sous-version",3=>"Version"));
		$chk->setValue(0);
		$chk->setLocation(2,3);
		$chk->JavaScript = "var type=current.getValue();
parent.get('incBtn').setEnabled(type!=0);";
		$xfer_result->addComponent($chk);

		$btn=new Xfer_Comp_Button('incBtn');
		$btn->setAction(new Xfer_Action("","ok.png","CORE","incVersion",FORMTYPE_MODAL,CLOSE_NO));
		$btn->setLocation(3,3);
		$xfer_result->addComponent($btn);

		if ($modify) {
		      $xfer_result->addAction(new Xfer_Action("_Commit","","CORE","commitGitExt",FORMTYPE_MODAL,CLOSE_NO));
		      $xfer_result->addAction(new Xfer_Action("_Annuler","","CORE","cancelGitExt",FORMTYPE_MODAL,CLOSE_NO));
		}
		$xfer_result->addAction(new Xfer_Action("_Pull","","CORE","pullGitExt",FORMTYPE_MODAL,CLOSE_NO));
		$xfer_result->addAction(new Xfer_Action("P_ush","","CORE","pushGitExt",FORMTYPE_MODAL,CLOSE_NO));
	}
	else {
	      $lbl=new Xfer_Comp_LabelForm('title3');
	      $lbl->setValue("{[newline]}{[bold]}{[center]}Cette extension n'est pas en lien avec un gestion GIT.{[newline]}Voulez-vous la rattacher à un repository?{[/center]}{[/bold]}");
	      $lbl->setLocation(0,1,2);
	      $xfer_result->addComponent($lbl);
	      $xfer_result->addAction(new Xfer_Action("_Lier","ok.png","CORE","linkGitExt",FORMTYPE_MODAL,CLOSE_YES));
	}
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
 
