<?php
//
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//


list($usec, $sec) = explode(" ", microtime());
// avant toute chose, on stipule qu'on retourne du text/plain
header("http-content: text/plain");

// on initialise la reponse
$REPONSE = "<REPONSES>";

require_once("PathInitial.inc.php");

$ApplisDir='../applis';
if (!is_dir($ApplisDir)) $ApplisDir='../extensions/applis';

// d?upage de l'XML de requette
require_once("CORE/XMLparse.inc.php");
$REQUETTE = "";
$nourlencode=array_key_exists("nourlencode", $GLOBAL);
if(array_key_exists("XMLinput", $GLOBAL)) {
	$p = &new COREParser();
	$xml_input=$GLOBAL["XMLinput"];
	if ($nourlencode || (substr($xml_input,0,3)!='%3C'))
		$XMLinput=str_replace(array("\"","\'"),"'",$xml_input);
	else
		$XMLinput=str_replace(array("\"","\'"),"'",urldecode($xml_input));
	$p->setInput($XMLinput);
	$p->parse();
	$REQUETTE = $p->getResult();
}

// gestion des exceptions
//require_once('CORE/addConficuration.inc.php'); 
require_once('CORE/xfer_exception.inc.php');
require_once('CORE/xfer.inc.php');

// gestion des droits
require_once('Main/rights.inc.php');

// gestion de l'authentification
// extraction du login pass ou de la ses du tableau REQUETTE
$found = false;
$lesRequettes = array();

if("xmlelement" == strtolower(get_class($REQUETTE))) $lesRequettes = $REQUETTE->getChildsByTagName("REQUETE");

foreach($lesRequettes as $req) {
	if($found) continue;
	
	// recup de l'extension et de l'action
	$extension = $req->getAttributeValue("EXTENSION");
	$action = $req->getAttributeValue("ACTION");
	if($extension == "common" && $action == "authentification") {
		$found = true;
		$paramTable = $req->getChildsByTagName("PARAM");
		foreach($paramTable as $par) {
			$GLOBAL[$par->getAttributeValue("NAME")] = $par->getCData();
		}
	}
}

try {
	require_once("Main/connection.inc.php"); 

	if($IS_CONNECTED) {
		// on est maintenant connect? la base de donn?et authentifi?soit par ses soit par login/pass)
		require_once("Main/BoucleReponse.inc.php");
		$REPONSE.= BoucleReponse($lesRequettes);
	}
} catch (Exception $e) {              // Devrait être attrapée
	require_once "CORE/xfer_exception.inc.php";
	$Xfer_erro=new Xfer_Container_Exception("CORE","coreIndex");
	$Xfer_erro->setData($e);
	$REPONSE.= $Xfer_erro->getReponseXML();
}

// les actions sont execut?, on exporte l'ensemble des reponses en XML
$REPONSE.="</REPONSES>";

if ($nourlencode) {
	print utf8_encode($REPONSE);
}
else {
	print urlencode(utf8_encode($REPONSE));
}
?>
