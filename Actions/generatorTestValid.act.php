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

function generatorTestValid($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Acknowledge($extensionname,"generatorTestValid",$Params);
	$xmlSaving=realpath($Params['xmlSaving']);
	$xslFile=realpath("ConvertSavingToPHP.xsl");
	if (is_file($xmlSaving) && is_file($xslFile) ) {
		$content=array();
		$proc_xsl = new DomDocument();
		$proc_xsl->loadXML(implode("", file($xslFile)));
		$proc_xml = new DomDocument();
		$proc_xml->loadXML(implode("", file($xmlSaving)));
		$xslt = new XsltProcessor();
		$xslt->importStylesheet($proc_xsl);
		$val = utf8_decode($xslt->transformToXML($proc_xml));
		$content=explode("\n",$val);
		$id=0;
		while ((substr($content[$id],0,5)=='<?xml') || (trim($content[$id])=='')) {
			unset($content[$id++]);
		}
		echo "<!-- val=$val / content=".print_r($content,true)." -->\n";
	
		require_once("Class/Extension.inc.php");
		require_once("Class/Test.inc.php");
		$id=$Params['test'];
		if (array_key_exists('classe',$Params)) {
			global $tablename;
			$tablename=$Params['classe'];
		}
		else	
			$tablename="";
		echo "<!-- id=$id / extensionname=$extensionname / tablename=$tablename -->\n";
		$extension=new Extension($extensionname);
		$test=new Test($id,$extension->Name,$tablename);
		foreach($content as $line)
			$test->CodeFunction[]=$line;
		$test->Write();
		$extension->IncrementBuild();
	}
	else {
	    require_once("../CORE/Lucterios_Error.inc.php");
	    throw new LucteriosException(IMPORTANT,"Fichier de XML '$xmlSaving' ou XSL '$xslFile' inconnu!");
	}
	return $xfer_result;
}

?>
