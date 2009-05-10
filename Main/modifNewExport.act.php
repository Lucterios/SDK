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

function sendModuleToUpdateServer($UpdateServerUrl,$project,$pass,$module,$arcFile)
{
	$txt="";
	require_once("HTTP/Request.php");
	$req =& new HTTP_Request($UpdateServerUrl."/actions/up.php");
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	//$req->addPostData("MAX_FILE_SIZE", "30000000");
	$req->addPostData("project", $project);
	$req->addPostData("pass", $pass);
	$req->addPostData("module", $module);
	$req->addFile("file", $arcFile);

	$response = $req->sendRequest();
	if (PEAR::isError($response))
        	$txt=$response->getMessage()."{[newline]}";
	else
        {
		$txt=trim($req->getResponseBody());
		if (strpos($txt,'<HTML>')!==false)
			$txt=str_replace(array('<','>'),array('{[',']}'),$txt);
                if ($txt=='')
                    $txt="Module '$module' envoyé.";
		$txt.="{[newline]}";
        }
	return $txt;
}

function modifNewExport($Params)
{
	require_once('../CORE/xfer.inc.php');
	$xfer_result=&new Xfer_Container_Acknowledge("CORE","modifNewExport",$Params);
	$serverurl=$Params['UrlServerUpdate'];
	$increm_version=$Params['IncVersion'];
	$extensions=split(';',$Params['ext']);
	$conf_file=file("CNX/Server_Update.dt");
	$Project=trim($conf_file[0]);
	$Pass=trim($conf_file[1]);
	$lasterror='';
	require_once("Class/Extension.inc.php");
	$appli_ext=new Extension('applis');

	foreach($extensions as $ext)
	{
		if ($ext=='') $ext='CORE';
		if ($ext==$appli_ext->Appli)
			$ext_obj=$appli_ext;
		else
			$ext_obj=new Extension($ext);
		switch($increm_version) {
			case 0: //non
				break;
			case 1: //Révision
				$ext_obj->IncrementRelease();
				break;
			case 2: //Sous-version
				$ext_obj->IncrementSubVersion();
				break;
			case 3: //Version
				$ext_obj->IncrementVersion();
				break;
		}
		$arch_file=$ext_obj->GetArchiveFile('.lpk');
		if (is_file($arch_file))
			unlink($arch_file);
		Extension::ArchiveExtension($ext_obj->Name,$arch_file,($ext!='CORE'),'gz');
		$lasterror.=sendModuleToUpdateServer($serverurl,$Project,$Pass,$ext,$arch_file);
		unlink($arch_file);	
	}
	$xfer_result->message($lasterror,1);
	return $xfer_result;
} 
