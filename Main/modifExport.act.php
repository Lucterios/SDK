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

	$fields = array();
	$fields["MAX_FILE_SIZE"]="10000000";
	if (($module!='CORE') && ($module!='applis'))
		$fields["type"]="1";
	else
		$fields["type"]="3";
	if ($module=='CORE')
		$fields["module"]="serveur";
	elseif ($module=='applis')
		$fields["module"]=$appli;
        else
		$fields["module"]=$module;
	$fields["description"]=$description;
	$fields["applis"]=$appli;
	$fields["max"]=$versions[0];
	$fields["min"]=$versions[1];
	$fields["rel"]=$versions[2];
	$fields["build"]=$versions[3];
	$fields["lefic"]="@$arcFile";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $UpdateServerUrl."/addPackage.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	$txt = trim(curl_exec($ch));
	$txt="Module '$module' envoyé.";
	$txt.="{[newline]}";
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
