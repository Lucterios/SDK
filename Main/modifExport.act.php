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

require_once('../CORE/xfer.inc.php');

function sendModuleToUpdateServer($UpdateServerUrl,$module,$description,$appli,$versions,$arcFile)
{
	$txt="";
	require_once("HTTP/Request.php");
	$req =& new HTTP_Request($UpdateServerUrl."/addPackage.php");
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	$req->addPostData("MAX_FILE_SIZE", "10000000");
	if (($module!='CORE') && ($module!='applis'))
		$req->addPostData("type", "1");
	else
		$req->addPostData("type", "3");
	if ($module=='CORE')
		$req->addPostData("module", "serveur");
	elseif ($module=='applis')
		$req->addPostData("module", $appli);
        else
		$req->addPostData("module", $module);
	$req->addPostData("description", $description);
	$req->addPostData("applis", $appli);
	$req->addPostData("max", $versions[0]);
	$req->addPostData("min", $versions[1]);
	$req->addPostData("rel", $versions[2]);
	$req->addPostData("build", $versions[3]);
	$req->addFile("lefic", $arcFile);

	$response = $req->sendRequest();
	if (PEAR::isError($response))
        	$txt=$response->getMessage()."{[newline]}";
	else
        {
		$txt=trim($req->getResponseBody());
                if ($txt=='')
                    $txt="Module '$module' envoyé.";
		$txt.="{[newline]}";
        }
	return $txt;
}

function modifExport($Params)
{
	$xfer_result=&new Xfer_Container_Acknowledge("CORE","modifExport",$Params);
	$serverurl=$Params['serverurl'];
	$is_increm_version=($Params['IncVersion']!='n');
	$extensions=split(';',$Params['ext']);
	$lasterror='';
	foreach($extensions as $ext)
	{
		require_once("Class/Extension.inc.php");
		if ($ext=='') $ext='CORE';
		$ext_obj=new Extension($ext);
		if ($is_increm_version)
		{
			$ext_obj->IncrementSubVersion();
			$ext_obj->Write();
		}
		$arch_file=$ext_obj->GetArchiveFile();
		if (is_file($arch_file))
			unlink($arch_file);
		Extension::ArchiveExtension($ext,$arch_file,($ext!='CORE') && ($ext!='applis'));
		$lasterror.=sendModuleToUpdateServer($serverurl,$ext_obj->Name,$ext_obj->Titre,$ext_obj->Appli,$ext_obj->Version,$arch_file);
		unlink($arch_file);	
	}
	$xfer_result->message($lasterror,1);
	return $xfer_result;
} 
