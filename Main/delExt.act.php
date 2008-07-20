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

function delExt($Params)
{
	$xfer_result=&new Xfer_Container_Acknowledge("CORE","delExt",$Params);
	$ext=$Params['ext'];
	require_once("CORE/extensionManager.inc.php");
	$extension=new Extension($ext,Extension::getFolder($ext,"../"));
	$deps=$extension->getDependants(array(),'../');
	if (count($deps)==0)
		$text="";
	else {
		$text="{[newline]}Cette extension dépent d'autres extensions:";
		foreach($deps as $dep)
			$text.="{[newline]} - $dep";
	}

	if ($xfer_result->Confirme("Etes-vous sûre de vouloir supprimer l'extension '$ext'?$text{[newline]}Cela supprimera toutes les données en base.")) 
	{
		$server_name=$_SERVER["SERVER_NAME"];
		$server_port=$_SERVER["SERVER_PORT"];
		$server_dir=$_SERVER["PHP_SELF"];
		if ($server_dir[0]=='/')
			$server_dir=substr($server_dir,1);
		$sep=strrpos($server_dir,'/');
		$server_dir=substr($server_dir,0,$sep);
		$refresh_url="http://$server_name:$server_port/$server_dir/DeleteModule.php?extensionname=$ext";
		require_once("HTTP/Request.php");
		$req =& new HTTP_Request($refresh_url);
		$req->setMethod(HTTP_REQUEST_METHOD_GET);
		$err=$req->sendRequest();
		if (!PEAR::isError($err))
			$message.=$req->getResponseBody();
		else 
			$message.=$err->getMessage();
		$xfer_result->redirectAction(new Xfer_Action('menu','','CORE','menu'));
	}
	return $xfer_result;
}

?>
 
