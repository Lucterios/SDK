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
	if(! class_exists('XsltProcessor') || ! class_exists('DomDocument'))die('processeur XSLT non installe!');
	$proc_xsl = new DomDocument();
	$proc_xsl->loadXML($xsldata);
	$proc_xml = new DomDocument();
	$proc_xml->loadXML($xmldata);
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($proc_xsl);
	$obj = $xslt->transformToDoc($proc_xml);
	if (method_exists($obj,'saveXML')) {
		$obj->encoding = 'ISO-8859-1';
		$res = $obj->saveXML();
	}
	else
		$res=$xmldata;
	return $res;
}


function performTests($Params)
{
	$xfer_result=&new Xfer_Container_Custom("CORE","performTests",$Params);	
	$xfer_result->Caption="Tests unitaires";
	$extensionname=$Params['extensionname'];
	$dbuser=$Params['dbuser'];
	$dbpass=$Params['dbpass'];
	$dbname=$Params['dbname'];
	$testnum=$Params['testnum'];
	$delete=($Params['delete']=='n')?'false':'true';
	
	$lbl=new Xfer_Comp_LabelForm('titlelbl');
	$lbl->setValue("{[underline]}{[bold]}{[center]}Resultat des tests{[/center]}{[/bold]}{[/underline]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
	
	$query="http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].dirname(dirname($_SERVER['SCRIPT_NAME']))."/Tests.php?extension=$extensionname&dbuser=$dbuser&dbname=$dbname&dbpass=$dbpass&num=$testnum&delete=$delete";
	$Rep = file($query);
	if(($Rep!==false) && (count($Rep)>0)) {
		$rep=trim(implode("\n", $Rep));
		$rep=str_replace('{[br /]}','',$rep);
		$rep=str_replace("\n\n\n\n","\n",$rep);
		$rep=str_replace("\n\n\n","\n",$rep);
		$rep=str_replace("\n\n","\n",$rep);
		$rep=str_replace("\n",'{[newline]}',$rep);
		$Response="";
		if (preg_match('/(.*)<testsuite(.*)<\/testsuite>(.*)/', $rep, $replistxml)>0) {
			//echo "<!--- replistxml".print_r($replistxml,true)." -->\n";
			$xml_res="<testsuite".$replistxml[2]."</testsuite>";
			$Resp_tmp = TransformXsl($xml_res,implode("\n",file("Main/unittests.xsl")));		
			$Response.= trim(str_replace('<?xml version="1.0" encoding="ISO-8859-1"?>',"",$Resp_tmp));
			$rep=trim($replistxml[1]).trim($replistxml[3]);
			if ($rep!='')
				$Response.= '{[newline]}{[hr/]}{[newline]}';
		}
		while (preg_match('/(.*)<!--(.*)-->(.*)/', $rep, $replist)>0) {
			//echo "<!--- replist".print_r($replist,true)." -->\n";
			$rep=trim($replist[1]).trim($replist[2]).'{[newline]}'.trim($replist[3]);
		}
		$rep=str_replace('<','{[',$rep);
		$rep=str_replace('>',']}',$rep);
		$rep=str_replace('{[b]}/','{[newline]}{[bold]}/',$rep);
		$rep=str_replace('{[/b]}:','{[/bold]}{[newline]}&#160;&#160;&#160;',$rep);
		$rep=str_replace('{[b]}','{[bold]}',$rep);
		$rep=str_replace('{[/b]}','{[/bold]}',$rep);
		$Response.= $rep;
	}
	else
		$Response = "Erreur '$query'{[newline]}$Rep";
	
	$lbl=new Xfer_Comp_LabelForm('resultlbl');
	$lbl->setValue($Response);
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);

	$xfer_result->addAction(new Xfer_Action("_Refresh","","CORE","performTests",FORMTYPE_REFRESH,CLOSE_NO));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
