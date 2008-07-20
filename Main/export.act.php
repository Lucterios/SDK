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

function export($Params)
{
	$xfer_result=&new Xfer_Container_Custom("CORE","export",$Params);
	$xfer_result->Caption="Distribuer votre application";

	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[bold]}{[center]}Distribuer votre application{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;

	require_once("Class/Extension.inc.php");
	$mods=Extension::GetList($cnx);
	list($UrlServerUpdate,)=file("CNX/ServerUpdate.dt");
	$UrlServerUpdate=trim($UrlServerUpdate);

	$versDist=array();
	$clientDist=array();
	foreach($mods as $mod_name=>$mod_ver)
		$versDist[$mod_name]="?.?.?.?";
	require_once("../CORE/setup_param.inc.php");
	if (is_file("../applis/setup.inc.php"))
		require("../applis/setup.inc.php");
	else
		require("../extensions/applis/setup.inc.php");
	$query=$UrlServerUpdate."index.php?act=list&applis=".$extention_appli;

	$Rep = file($query);
	if(($Rep!==false) && (count($Rep)>0))
	{
		$Response = implode("\n", $Rep);
		// decoupage de la liste distante
		require_once("../CORE/XMLparse.inc.php");
		$p = new COREParser();
		$p->setInputString($Response);
		$p->parse();
		$LIST = $p->getResult();
		$Common_childs = $LIST->getChildsByTagName("COMMON");
		$Extension_childs = $LIST->getChildsByTagName("EXTENSION");
		$client_childs = $LIST->getChildsByTagName("CLIENT");

		foreach($Common_childs as $Common_child)
		{
			$module = $Common_child->getAttributeValue("ID");
			if ($module=='serveur')
				$versDist['CORE']=$Common_child->getAttributeValue("VERSION");
			elseif ($module==$extention_appli)
				$versDist['applis']=$Common_child->getAttributeValue("VERSION");
		}
		foreach($Extension_childs as $Extension_child)
		{
			$module = $Extension_child->getAttributeValue("EXTENSION");
			$vers=$Extension_child->getAttributeValue("VERSION");
			$versDist[$module]=$vers;
		}
		foreach($client_childs as $client_child)
		{
			$module = $client_child->getAttributeValue("ID");
			$clientDist[$module]=$client_child->getAttributeValue("VERSION");
		}		
	}

	$grid=new Xfer_Comp_Grid('ext');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Version Local",4);
	$grid->newHeader('C',"Version  sur le Serveur",4);
	$grid->newHeader('D',"Vérou",4);

	foreach($mods as $mod_name=>$mod_ver)
	{
		$grid->setValue($mod_name,'A',$mod_name);
		$grid->setValue($mod_name,'B',$mod_ver);
		$grid->setValue($mod_name,'C',$versDist[$mod_name]);
		$grid->setValue($mod_name,'D',$lock);
	}
	$grid->setLocation(0,1,2);
	$grid->addAction(new Xfer_Action("_Exporter","","CORE","modifExport",FORMTYPE_MODAL,CLOSE_NO,SELECT_MULTI));
	$xfer_result->addComponent($grid);

	$lbl=new Xfer_Comp_LabelForm('serverlbl');
	$lbl->setValue("{[bold]}Serveur{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);

	$edt=new Xfer_Comp_Edit('serverurl');
	$edt->setValue($UrlServerUpdate);
	$edt->setLocation(1,2);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('inclbl');
	$lbl->setValue("{[bold]}Incrémenter la version{[/bold]}");
	$lbl->setLocation(0,3);
	$xfer_result->addComponent($lbl);

	$chk=new Xfer_Comp_Check('IncVersion');
	$chk->setValue('n');
	$chk->setLocation(1,3);
	$xfer_result->addComponent($chk);

	$link=new Xfer_Comp_LinkLabel('link1');
	$link->setValue("Distribuer sur un autre serveur");
	$link->setLink("$UrlServerUpdate/?act=distrib");
	$link->setLocation(0,4,2);
	$xfer_result->addComponent($link);

	$link=new Xfer_Comp_LinkLabel('link2');
	$link->setValue("Interface de téléchargement du Serveur");
	$link->setLink("$UrlServerUpdate/upload.php");
	$link->setLocation(0,5,2);
	$xfer_result->addComponent($link);

	$lbl=new Xfer_Comp_LabelForm('clinetlbl');
	$lbl->setValue("{[bold]}{[center]}Clients enregistrés{[/center]}{[/bold]}");
	$lbl->setLocation(0,6,2);
	$xfer_result->addComponent($lbl);

	$grid=new Xfer_Comp_Grid('client');
	$grid->newHeader('A',"Client",4);
	$grid->newHeader('B',"Version  sur le Serveur",4);
	foreach($clientDist as $mod_name=>$mod_ver)
	{
		$grid->setValue($mod_name,'A',$mod_name);
		$grid->setValue($mod_name,'B',$mod_ver);
	}
	$grid->setLocation(0,7,2);
	$xfer_result->addComponent($grid);

	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
