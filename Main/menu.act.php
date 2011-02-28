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

require_once('../CORE/xfer_menu.inc.php');

function exportAllowed($ServerList)
{
	list($UrlServerUpdate,)=file($ServerList);
	$UrlServerUpdate=trim($UrlServerUpdate);
	return (file_get_contents($UrlServerUpdate)!==false);
}

function menu($Params)
{
try {
	require_once "../CORE/setup_param.inc.php";
	require "./setup.inc.php";
	$dep=$depencies[0];
	$serv_depend_max=trim($dep->version_majeur_max).'.'.trim($dep->version_mineur_max);
	//$serv_depend_min=trim($dep->version_majeur_min).'.'.trim($dep->version_mineur_min);
	require "../CORE/setup.inc.php";
	$serv_version=trim($version_max).'.'.trim($version_min);
	if ($serv_depend_max!=$serv_version)
	{
		require_once "../CORE/Lucterios_Error.inc.php";
		throw new LucteriosException(IMPORTANT,"{[center]}Le serveur a pour version {[bold]}$serv_version{[/bold]} mais ce SDK ne support que la version {[bold]}$serv_depend_max{[/bold]}.{[newline]}Mettez à jour vos outils.{[/center]}");
	} 

	require_once("Class/Extension.inc.php");
	global $CNX_OBJ;

	$xfer_result=&new Xfer_Container_Menu("CORE","menu",$Params);
	$xfer_result->Caption='Menu de l application';

	$menu_tools = new Xfer_Menu_Item('tool0','Les outils','config.png','','',0,'',"Pour vérouiller, déployer ou configurer votre application.");

	$menu_tool1 = new Xfer_Menu_Item('tool1','Réservations','reserve.png','CORE','reserveExtension',0,'ctrl R',"Pour réserver une extension de l'application.");
	$menu_tools->addSubMenu($menu_tool1);
	if (exportAllowed("CNX/Server_Update.dt")) {
		$menu_tool2b = new Xfer_Menu_Item('tool2','Déploiement','export.png','CORE','newExport',0,'ctrl D',"Pour dépoyer une extension de l'application sur le nouveau serveur.");
		$menu_tools->addSubMenu($menu_tool2b);
	}
	$menu_tool3 = new Xfer_Menu_Item('tool3','Mot de Passe','passwd.png','CORE','password',1,'ctrl P',"Pour changer votre mot de passe.");
	$menu_tools->addSubMenu($menu_tool3);
	if ($CNX_OBJ->Name=="admin") {
		$menu_tool4 = new Xfer_Menu_Item('tool4','Utilisateurs','user.png','CORE','user',0,'ctrl U',"Pour gérer les utilisateurs de ce SDK.");
		$menu_tools->addSubMenu($menu_tool4);
		$menu_tool4 = new Xfer_Menu_Item('tool5','Paramètrages','param.png','CORE','parameter',0,'ctrl U',"Pour gérer les paramètres de ce SDK.");
		$menu_tools->addSubMenu($menu_tool4);
	}
	$menu_tool5 = new Xfer_Menu_Item('tool6','Log','edit.png','CORE','visuLog',0,'ctrl L',"Visualisation du log.");
	$menu_tools->addSubMenu($menu_tool5);
	$xfer_result->addSubMenu($menu_tools);

	$menu_general = new Xfer_Menu_Item('general0','Général','general.png','','',0,'',"Pour éditer ou modifier le coeur.");
	$default_ext=array("CORE"=>array('ctrl alt C','ctrl alt shift C'),"applis"=>array('ctrl alt I','ctrl alt shift I'));
	$index=0;
	foreach($default_ext as $ext=>$shortcut)
		if ($CNX_OBJ->IsViewModule($ext))
		{
			$ext_obj=new Extension($ext);
			$index++;
			$help="Pour modifier {[underline]}".$ext_obj->Titre."{[/underline]}{[newline]}";
			$help.="Version:".$ext_obj->GetVersion();
			$menu_general_sub = new Xfer_Menu_Item("general$index",$ext_obj->Name,'generalsub.png',$ext,'',0,'',$help);
			
			$menu_general_sub1 = new Xfer_Menu_Item("generalParam$index","Paramètres ".$ext_obj->Name,'parameters.png',$ext,'ListExtension',0,$shortcut[0],"Pour modifier les paramètres de {[underline]}".$ext_obj->Titre."{[/underline]}");
			$menu_general_sub->addSubMenu($menu_general_sub1);
			
			$menu_general_sub2 = new Xfer_Menu_Item("generalScript$index","Scripts ".$ext_obj->Name,'script.png',$ext,'ListScript',0,$shortcut[1],"Pour modifier les scripts de {[underline]}".$ext_obj->Titre."{[/underline]}");
			$menu_general_sub->addSubMenu($menu_general_sub2);

			$menu_general->addSubMenu($menu_general_sub);
		}
	if ($index!=0) $xfer_result->addSubMenu($menu_general);

	$menu_ext = new Xfer_Menu_Item('ext0','Les extensions','extension.png','','',0,'',"Pour éditer ou modifier une extension.");
	$ext_list=Extension::getList();
	$index=0;
	foreach($ext_list as $ext => $ext_version)
		if ($CNX_OBJ->IsViewModule($ext))
		{
			$ext_name=str_replace('_','-',$ext);
			$ext_obj=new Extension($ext);
			$index++;
			$help="Pour modifier {[underline]}".$ext_obj->Titre."{[/underline]}{[newline]}";
			$help.="Version:$ext_version";
			$menu_ext_sub = new Xfer_Menu_Item("ext$index",$ext_name,'extensionsub.png',$ext,'',0,'',$help);

			$menu_ext_sub1 = new Xfer_Menu_Item("extParam$index","Paramètres ".$ext_name,'parameters.png',$ext,'ListExtension',0,'',"Pour modifier les paramètres de {[underline]}".$ext_obj->Titre."{[/underline]}");
			$menu_ext_sub->addSubMenu($menu_ext_sub1);
			
			$menu_ext_sub2 = new Xfer_Menu_Item("extScript$index","Scripts ".$ext_name,'script.png',$ext,'ListScript',0,'',"Pour modifier les scripts de {[underline]}".$ext_obj->Titre."{[/underline]}");
			$menu_ext_sub->addSubMenu($menu_ext_sub2);

			$menu_ext->addSubMenu($menu_ext_sub);
		}
	if ($index!=0) $xfer_result->addSubMenu($menu_ext);

}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
