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

function commitGitExt($Params)
{
	if (array_key_exists('git_message',$Params)) {
		$xfer_result=&new Xfer_Container_Acknowledge("CORE","commitGitExt",$Params);
		$xfer_result->Caption="Commit GIT";
	
		$git_message=trim($Params['git_message']);
		$git_message=implode("\n",explode("{[newline]}",$git_message));
		$ext=$Params['ext'];
		require_once("Class/Extension.inc.php");
		$extObj=new Extension($ext);
		$repo=$extObj->GetGitRepoObj();
		$ret=$repo->commit($git_message);
		$ret=implode("{[newline]}",explode("\n",$ret));
		$xfer_result->message($ret);
	}
	else {
		$ext=$Params['ext'];
		require_once("Class/Extension.inc.php");
		$extObj=new Extension($ext);
		$repo=$extObj->GetGitRepoObj();

		$xfer_result=&new Xfer_Container_Custom("CORE","commitGitExt",$Params);
		$xfer_result->Caption="Ajout de repository";

		$lbl=new Xfer_Comp_LabelForm('git_messageLbl');
		$lbl->setLocation(0,0);
		$lbl->setValue('{[bold]}Message GIT{[/bold]}');
		$xfer_result->addComponent($lbl);
	
		$last_msg=$repo->run('log -n 1 --pretty=format:"%s"');
		$lbl=new Xfer_Comp_Memo('git_message');
		$lbl->setLocation(1,0);
		$lbl->setValue(str_replace("\n","{[newline]}",$last_msg));
		$lbl->needed=true;
		$xfer_result->addComponent($lbl);
	
		$xfer_result->addAction(new Xfer_Action("_Valider","ok.png","CORE","commitGitExt"));
		$xfer_result->addAction(new Xfer_Action("_Annuler","cancel.png"));
	}
	return $xfer_result;
}

?>
 
