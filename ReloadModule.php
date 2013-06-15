<?php
//
//    This file is part of SDK Lucterios.
//
//    SDK Lucterios is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    SDK Lucterios is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Lucterios; if not, write to the Free Software
//    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//

require_once("PathInitial.inc.php");
require_once("conf/cnf.inc.php");
require_once("CORE/dbcnx.inc.php");
require_once "CORE/extensionManager.inc.php";

function refreshExtension($Ext)
{
	list($a,$b)=explode('/',$Ext->installComplete());
	$install=$Ext->message;
	$install=implode("{[newline]}",explode("\n",$install));
	if (!isset($act) && ($a!=$b))
		$install.="{[newline]}{[bold]}Des erreurs de mise à jours sont apparues ($a/$b).{[/bold]}";
	return $install;
}

if (isset($act)) {
	try{
		if (!$connect->connected)
			createDataBase();

		if (isset($extensionname) && ($extensionname!='')) 
		{
			$rootPath="../";
			$ext_path=Extension::getFolder($extensionname,$rootPath);
			$Ext=new Extension($extensionname,$ext_path);
			echo refreshExtension($Ext);
		}
		else
		{
			echo "{[center]}{[bold]}{[underline]}Reload{[/underline]}{[/bold]}{[/center]}{[newline]}";
			$rootPath="../";
			$install='';
			$set_of_ext=array();	
			$ext_list=getExtensions($rootPath);
			foreach($ext_list as $name=>$dir)
				$set_of_ext[]=new Extension($name,$dir);
			$set_of_ext=sortExtension($set_of_ext,$rootPath);
			foreach($set_of_ext as $ext)
			{
				echo "{[center]}{[bold]}".$ext->Name."{[/bold]}{[/center]}";
				echo refreshExtension($ext);
			}
		}
	}
	catch(Exception $e){
		echo "{[bold]}".$e->getMessage()."{[/bold]}{[newline]}";
		require_once ("FunctionTool.inc.php");
		$trace=getTraceException($e);
		echo implode("{[newline]}",explode("\n",$trace));
	}
}
else
	echo "Erreur!!";

?>
