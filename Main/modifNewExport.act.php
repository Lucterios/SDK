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
	$fields = array();
	//$fields["MAX_FILE_SIZE"]="30000000";
	$fields["project"]=$project;
	$fields["pass"]=$pass;
	$fields["module"]=$module;
	$fields["file"]="@$arcFile";
	 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $UpdateServerUrl."/actions/up.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	$txt = curl_exec($ch);	
	
	if (strpos($txt,'<HTML>')!==false)
		$txt=str_replace(array('<','>'),array('{[',']}'),$txt);
	if ($txt=='')
	    $txt="Module '$module' envoyé.";
	$txt.="{[newline]}";
	return $txt;
}

function modifNewExport($Params)
{
	require_once('../CORE/xfer.inc.php');
	$xfer_result=&new Xfer_Container_Acknowledge("CORE","modifNewExport",$Params);
	$serverurl=$Params['UrlServerUpdate'];
	$extensions=explode(';',$Params['ext']);
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
		$arch_file=$ext_obj->GetArchiveFile('.lpk');
		if (is_file($arch_file))
			unlink($arch_file);
		Extension::ArchiveExtension($ext_obj->Name,$arch_file,($ext!='CORE'),'gz');
		if ($serverurl!='') {
			$lasterror.=sendModuleToUpdateServer($serverurl,$Project,$Pass,$ext,$arch_file);
		}
		else {
			if (!is_dir('./depoyed'))
			      mkdir('./depoyed',0777);
			$filename=realpath(".");
			$filename.="/depoyed/$ext";
			$filename.='_'.implode('-',$ext_obj->Version);
			$filename.='.lpk';
			copy($arch_file,$filename);
			chmod($filename, 0666);
			$lasterror.="Génération en '$filename'.{[newline]}";
		}
		unlink($arch_file);	
	}
	$xfer_result->message($lasterror,1);
	return $xfer_result;
} 
