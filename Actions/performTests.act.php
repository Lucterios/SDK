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
require_once('../CORE/xfer_custom.inc.php');

function TransformXsl($xmldata,$xsldata) {
	if( version_compare( phpversion(),'5','>=')) {
		if(! class_exists('XsltProcessor') || ! class_exists('DomDocument'))die('processeur XSLT non installe!');
		$proc_xsl = new DomDocument();
		$proc_xsl->loadXML($xsldata);
		$proc_xml = new DomDocument();
		$proc_xml->loadXML($xmldata);
		$xslt = new XsltProcessor();
		$xslt->importStylesheet($proc_xsl);
		$obj = $xslt->transformToDoc($proc_xml);
		$obj->encoding = 'ISO-8859-1';
		$res = $obj->saveXML();
	}
	else {
		$dom_xml = domxml_open_mem($xmldata);
		$dom_xsl = domxml_xslt_stylesheet($xsldata);
		$dom_result = $dom_xsl->process($dom_xml);
		$res = $dom_result->dump_mem( true,"ISO-8859-1");
	}
	return $res;
}


function performTests($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Custom($extensionname,"performTests",$Params);
	$xfer_result->Caption="Tests unitaires";
	$lbl=new Xfer_Comp_LabelForm('titlelbl');
	$lbl->setValue("{[underline]}{[bold]}{[center]}Resultat des tests{[/center]}{[/bold]}{[/underline]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
	
	$DBUnitTest=file("CNX/DBUnitTest.dt");
	$dbuser=trim($DBUnitTest[0]);
	$dbpass=trim($DBUnitTest[1]);
	$dbname=trim($DBUnitTest[2]);

	
	$query="http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].dirname(dirname($_SERVER['SCRIPT_NAME']))."/Tests.php?extensions=$extensionname&dbuser=$dbuser&dbname=$dbname&dbpass=$dbpass";
	$Rep = file($query);
	if(($Rep!==false) && (count($Rep)>0)) {
		$rep=trim(implode("\n", $Rep));
		if (substr($rep,0,10)=='<testsuite') {
			$Response = TransformXsl($rep,implode("\n",file("Actions/unittests.xsl")));
			$Response = trim(str_replace('<?xml version="1.0" encoding="ISO-8859-1"?>',"",$Response));
		}
		else {
			$Response = "{[center]}{[underline]}Tests unitaires:&#160;Erreur fatal{[/underline]}{[/center]}{[newline]}";
			$rep=str_replace('<','{[',$rep);
			$rep=str_replace('>',']}',$rep);
			$rep=str_replace('{[br /]}','',$rep);
			$rep=str_replace('{[b]}/','{[newline]}{[bold]}/',$rep);
			$rep=str_replace('{[/b]}:','{[/bold]}{[newline]}&#160;&#160;&#160;',$rep);
			$rep=str_replace('{[b]}','{[bold]}',$rep);
			$rep=str_replace('{[/b]}','{[/bold]}',$rep);

			$Response.= $rep;
		}
	}
	else
		$Response = "Erreur '$query'";
	
	$lbl=new Xfer_Comp_LabelForm('resultlbl');
	$lbl->setValue($Response);
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);

	$xfer_result->addAction(new Xfer_Action("_Refresh","",$extensionname,"performTests",FORMTYPE_REFRESH,CLOSE_NO));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
