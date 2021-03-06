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

if (function_exists('gc_enable')) {
	gc_enable();
}

require_once("PathInitial.inc.php");
require_once "CORE/extensionManager.inc.php";

if (isset($extensionname)) 
{
	try{
		$text='';
		$rootPath="../";
		$extDir=Extension::getFolder($extensionname,$rootPath);
		$extension=new Extension($extensionname,$extDir);
		$text.="extDir:$extDir";
		$deps=$extension->getDependants(array(),$rootPath);
		foreach($deps as $dep) {
			$ext_dep=new Extension($dep,Extension::getFolder($dep,$rootPath));
			$ext_dep->delete();
			$text.=$ext_dep->message;
		}
		$text.=" ret:".$extension->delete();
		$text.=$extension->message;
		deleteDir($extDir);
		echo $text;
	}
	catch(Exception $e){
		echo "{[bold]}".$e->getMessage()."{[/bold]}{[newline]}";
		echo implode("{[newline]}",explode("\n",$e->getErrorTrace()));
	}
}
else
{
	echo " !! ERROR !! ";
}

?>
