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
require_once('Main/connection.inc.php');

function newExport($Params)
{
	$xfer_result=&new Xfer_Container_Custom("CORE","newExport",$Params);
	$xfer_result->Caption="Distribuer votre application";

	$lbl=new Xfer_Comp_Image('img');
	$lbl->setValue("export.png");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[newline]}{[bold]}{[center]}Distribuer votre application sur un serveur de mise à jours{[/center]}{[/bold]}");
	$lbl->setLocation(1,0);
	$xfer_result->addComponent($lbl);

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;

	$conf_file=file("CNX/Server_Update.dt");
	$Project=trim($conf_file[0]);
	$Pass=trim($conf_file[1]);
	for($i=2;$i<count($conf_file);$i++) {
		$tmp=trim($conf_file[$i]);
		if ($tmp=='')
			$UrlServers['']='---';
		else
			$UrlServers[$tmp]=$tmp;
	}
	if (count($UrlServers)==0) {
		require_once("CORE/Lucterios_Error.inc.php");
		throw new LucteriosException(IMPORTANT,"Aucun serveur de mise à jours");
	}
	if (array_key_exists('UrlServerUpdate',$Params))
		$UrlServerUpdate=trim($Params['UrlServerUpdate']);
	else
		$UrlServerUpdate=trim($conf_file[2]);
	if ($UrlServerUpdate!='')
		$query=$UrlServerUpdate."/actions/liste.php?project=$Project";
	else
		$query="";

	require_once("Class/Extension.inc.php");
	$ext=new Extension('applis');
	$application_name=$ext->Appli;

	$mods=Extension::GetList($cnx);
	$versDist=array();
	$clientDist=array();
	foreach($mods as $mod_name=>$mod_ver)
	{
		if ($mod_name=='applis')
			$mod_name=$application_name;
		$versDist[$mod_name]="???";
	}
	require_once("../CORE/setup_param.inc.php");
	if (is_file("../applis/setup.inc.php"))
		require("../applis/setup.inc.php");
	else
		require("../extensions/applis/setup.inc.php");

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
		$Extension_childs = $LIST->getChildsByTagName("MODULE");
		foreach($Extension_childs as $Extension_child)
		{
			$module = $Extension_child->getAttributeValue("module");
			$vmax=$Extension_child->getAttributeValue("vmax");
			$vmin=$Extension_child->getAttributeValue("vmin");
			$vrel=$Extension_child->getAttributeValue("vrel");
			$vbuild=$Extension_child->getAttributeValue("vbuild");
			$versDist[$module]="$vmax.$vmin.$vrel.$vbuild";
		}
		foreach($versDist as $mod_name=>$val)
			if ($val=='???') 
				$versDist[$mod_name]="{[italic]}Inconnu{[/italic]}";
	}
	else
	{
		foreach($versDist as $mod_name=>$val)
			if ($val=='???') 
				$versDist[$mod_name]="{[italic]}Pas de serveur{[/italic]}";
	}

	$grid=new Xfer_Comp_Grid('ext');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Version Local",4);
	$grid->newHeader('C',"Version Distant",4);

	$current_mods=$mods;
	foreach($current_mods as $mod_name=>$mod_ver)
	{
		if ($cnx->CanWriteModule($mod_name)) {
			if ($mod_name=='applis')
				$mod_name=$application_name;
			$grid->setValue($mod_name,'A',$mod_name);
			$grid->setValue($mod_name,'B',$mod_ver);
			$grid->setValue($mod_name,'C',$versDist[$mod_name]);
		} else {
			unset($mods[$mod_name]);
		}
	}
	$grid->setLocation(0,1,2);
	$grid->addAction(new Xfer_Action("_Exporter","","CORE","modifNewExport",FORMTYPE_MODAL,CLOSE_NO,SELECT_MULTI));
	$xfer_result->addComponent($grid);

	$lbl=new Xfer_Comp_LabelForm('serverlbl');
	$lbl->setValue("{[bold]}Serveur{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);

	$edt=new Xfer_Comp_Select('UrlServerUpdate');
	$edt->setValue($UrlServerUpdate);
	$edt->setSelect($UrlServers);
	$edt->setLocation(1,2);
	$edt->setAction(new Xfer_Action("","","CORE","newExport",FORMTYPE_REFRESH,CLOSE_NO));
	$xfer_result->addComponent($edt);

	if ($UrlServerUpdate!='') {
		$link=new Xfer_Comp_LinkLabel('link');
		$link->setValue("Site web du serveur de mise à jours");
		$link->setLink("$UrlServerUpdate");
		$link->setLocation(0,4,2);
		$xfer_result->addComponent($link);
	}

	$otherDist=array();
	foreach($versDist as $mod=>$ver)
	{
		if ($mod==$application_name)
			$mod_name='applis';
		else
			$mod_name=$mod;
		if (!array_key_exists($mod_name,$mods))
			$otherDist[$mod]=$ver;
	}
	
	if (count($otherDist)>0) {
		$lbl=new Xfer_Comp_LabelForm('otherlbl');
		$lbl->setValue("{[bold]}{[center]}Autres modules{[/center]}{[/bold]}");
		$lbl->setLocation(0,6,2);
		$xfer_result->addComponent($lbl);
	
		$grid->newHeader('A',"Nom",4);
		$grid=new Xfer_Comp_Grid('other');
		$grid->newHeader('B',"Version Distant",4);
		foreach($otherDist as $mod_name=>$mod_ver)
		{
			$grid->setValue($mod_name,'A',$mod_name);
			$grid->setValue($mod_name,'B',$mod_ver);
		}
		$grid->setLocation(0,7,2);
		$xfer_result->addComponent($grid);
	}

	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
