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


//@BEGIN@
function studyReponse($current_reponse)
{
	$Params=array();
	if (is_string($current_reponse))
	{
		$p = &new COREParser();
		$p->setInputString($current_reponse);
		$p->parse();
		$Reponse_trans = $p->getResult();
		if (("xmlelement" == strtolower(get_class($Reponse_trans))) && ($Reponse_trans->getTagName()=="REPONSE"))
		{
			if ($Reponse_trans->getAttributeValue('observer')=='CORE.Exception')
				return null;
			$lesReponses = $Reponse_trans->getChildsByTagName("PARAM");
			foreach($lesReponses as $uneReponse)
				$params[utf8_decode($uneReponse->getAttributeValue('name'))]=utf8_decode($uneReponse->getCData());
			return $params;
		}
		else
			return null;
	}
	else if (is_object($current_reponse))
	{
		if ("Xfer_Container_Exception" == strtolower(get_class($current_reponse))
)
			return null;
		else
			return $current_reponse->m_context;
	}
	else
		return null;
}

function BoucleReponse($lesRequettes,$internal=false)
{
	global $login,$dbcnf;
	require_once("../CORE/Lucterios_Error.inc.php");
	require_once("../CORE/xfer.inc.php");
	$REPONSE="";
	$params = array();
	foreach($lesRequettes as $req)
	{
		$extension = "";
		$action = "";
		try {
			$current_reponse="";
			global $extension;
			$extension = $req->getAttributeValue("EXTENSION");
			$action = utf8_decode ($req->getAttributeValue("ACTION"));

			if($extension=="common" && $action=="authentification") {
				// l'authentification est deja g?e plus haut, on la zap!
				continue;
			}

			// on recupere les param?es pour l'action
			$paramTable = $req->getChildsByTagName("PARAM");
			foreach($paramTable as $par) {
				$params[utf8_decode($par->getAttributeValue("NAME"))] = utf8_decode($par->getCData());
			}

			// on sait maintenant qu'on ?es droits d'executer l'action voulue
			$CURRENT_PATH=".";
			if (strtoupper($extension)=="CORE") {
				$EXT_FOLDER="$CURRENT_PATH/Main";
				if (!is_file("$EXT_FOLDER/$action.act.php"))
					$EXT_FOLDER="$CURRENT_PATH/Actions";
			}
			else
				$EXT_FOLDER="$CURRENT_PATH/Actions";
			$ACTION_FILE_NAME = "$EXT_FOLDER/$action.act.php";
			if (!is_dir($EXT_FOLDER))
			{
				// l'extension n'existe pas
				$current_reponse= xfer_returnError($extension, $action, $params, "Extension '$extension' inconnue !");
			}
			else if (!is_file($ACTION_FILE_NAME))
			{
				// le fichier n'existe pas dans l'extension
				$current_reponse=  xfer_returnError($extension, $action, $params, "Action '$action' inconnue !");
			}
			else
			{
				require_once $ACTION_FILE_NAME;
				if (!function_exists($action))
				{
					// la fonction n'existe pas dans le fichier
					$current_reponse=xfer_returnError($extension,$action,$params,"Function inconnue !");
				}
				else
				{
					// l'action existe, on la lance:
					$current_reponse=$action($params,$extension);
				}
			}

			if (is_string($current_reponse)){
				if ($current_reponse!="")
					$REPONSE.=$current_reponse."\n";
				else
					$REPONSE.=xfer_returnError($extension,$action,$params,"Résultat vide!!");

			}
			else{
				$REPONSE.=$current_reponse->getReponseXML()."\n";
			}

			if ($internal)
			{
				if (($params = studyReponse($current_reponse))==null)
					return $REPONSE;
			}
			else
				$params = array();
		} catch (Exception $e) {              // Devrait être attrapée
			require_once "../CORE/xfer_exception.inc.php";
  			$Xfer_erro=new Xfer_Container_Exception("CORE","coreIndex");
	  		$Xfer_erro->setData($e);
  			$REPONSE.= $Xfer_erro->getReponseXML()."\n";
		}
	}
	return $REPONSE;
}

//@END@
?>
