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

function addRepoValid($Params)
{
	$xfer_result=new Xfer_Container_Acknowledge("CORE","addRepoValid",$Params);
	$ext=$Params['ext'];
	$new_repo_url=$Params['newRepo'];
	require_once("Class/Extension.inc.php");
	$extObj=new Extension($ext);
	$repo=$extObj->GetGitRepoObj();
	$ret.="";
	try {
		$ret.=$repo->run("remote remove origin");
	} catch(Exception $e) {}
	$ret.=$repo->run("remote add origin '$new_repo_url'");
	$ret.=$repo->run("config branch.master.remote origin");
	$ret.=$repo->run("config branch.master.merge refs/heads/master");
	$ret.=$repo->run("config push.default simple");
    include_once('Class/Git.php');
	check_git_server($new_repo_url);
	if (trim($ret)=="") {
		$xfer_result->redirectAction(new Xfer_Action("", "", "CORE", $Params['act_origin'],FORMTYPE_MODAL));
	} 
	else {
		$ret=implode("{[newline]}",explode("\n",$ret));
		$xfer_result->message($ret);
	}
	return $xfer_result;
}

?>
 
