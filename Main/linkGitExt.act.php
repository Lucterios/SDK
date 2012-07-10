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

function moveItem($itemSource,$itemTarget) {
	if (is_file($itemSource)) {
		if (is_file($itemTarget))
			unlink($itemTarget);
		rename($itemSource,$itemTarget);
	}
	else if (is_dir($itemSource)) {
		if (!is_dir($itemTarget))
			mkdir($itemTarget);
		$dh=opendir($itemSource);
		while (($file = readdir($dh)) != false)
		{
			if (($file!='.') && ($file!='..')) {
				moveItem($itemSource.'/'.$file,$itemTarget.'/'.$file);
			}
		}
		deleteDir($itemSource);
	}
}

function linkGitExt($Params)
{
	$xfer_result=&new Xfer_Container_Acknowledge("CORE","linkGitExt",$Params);
	require_once("Class/Extension.inc.php");
	require_once("FunctionTool.inc.php");
	$extName=$Params['ext'];
	if ($extName!='CORE')
	      $extDir = Extension::GetExtDir($extName);
	else
	      $extDir = "../";
	if ($extName!='applis')
		$repo_name=trim($extName);
	else {
		$extObj=new Extension($extName);
		$repo_name=trim($extObj->Appli);
	}
	
	$msg="";
	$conf_file=file("CNX/Conf_Manage.dt");
	$gitUser=$conf_file[0];
	$gitEmail=$conf_file[1];
	$tmp_dir="./tmp/$extName/";
	$sucess=false;
	for($i=2;$i<count($conf_file);$i++) {
	      $conf_item=$conf_file[$i];
	      if (!$sucess) {
		    try {
			$repo_url=trim($conf_item).trim($repo_name).".git";
			$msg.="$repo_url:";
			deleteDir($tmp_dir);
			Extension::CreateGitRepoByClone($tmp_dir,$repo_url,$gitUser,$gitEmail);
			$msg.=" importé";
			moveItem($tmp_dir,$extDir);
			$sucess=true;
		    }catch(Exception $e) {
			require_once ("FunctionTool.inc.php");
			$trace=getTraceException($e);
			echo "<!-- error:".$e->getMessage()."\ntrace:$trace -->\n";
			$msg.=" non supporté !";
		    }
		    $msg.="{[newline]}";
	      }
	}
	$xfer_result->message($msg,$sucess?XFER_DBOX_INFORMATION:XFER_DBOX_ERROR);
	return $xfer_result;
}

?>
 
