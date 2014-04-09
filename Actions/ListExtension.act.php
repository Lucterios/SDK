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

function ListExtension($Params,$extensionname)
{
	$xfer_result=new Xfer_Container_Custom($extensionname,"ListExtension",$Params);
	$xfer_result->Caption="Gestion des paramètres de l'extension '$extensionname'";

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	$ReadOnly=$cnx->IsReadOnly($extensionname);

	$lbl=new Xfer_Comp_Label('marge1');
	$lbl->setValue("");
	$lbl->setSize(10,200,10,200);
	$lbl->setLocation(0,1,1,4);
	$xfer_result->addComponent($lbl);
	$lbl=new Xfer_Comp_Label('marge2');
	$lbl->setValue("");
	$lbl->setSize(10,200,10,200);
	$lbl->setLocation(10,1,1,4);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('namelbl');
	$lbl->setValue("{[bold]}Nom{[/bold]}");
	$lbl->setLocation(1,0);
	$xfer_result->addComponent($lbl);
	$lbl=new Xfer_Comp_LabelForm('name');
	$lbl->setValue($extension->Name);
	$lbl->setLocation(2,0,3);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('extensiontitlelbl');
	$lbl->setValue("{[bold]}Titre{[/bold]}");
	$lbl->setLocation(1,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('extensiontitle');
	$edt->setValue($extension->Titre);
	$edt->setLocation(2,1,5);
	$edt->Needed=true;
	$xfer_result->addComponent($edt);

if ($extension->Name=='applis') {
	$lbl=new Xfer_Comp_LabelForm('extensionapplilbl');
	$lbl->setValue("{[bold]}Application{[/bold]}");
	$lbl->setLocation(1,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('extensionappli');
	$edt->setValue($extension->Appli);
	$edt->setLocation(2,2,5);
	$edt->Needed=true;
	$xfer_result->addComponent($edt);
}
if (($extension->Name!='applis') && ($extension->Name!='CORE')) {
	$lbl=new Xfer_Comp_LabelForm('extensionfamillelbl');
	$lbl->setValue("{[bold]}Famille{[/bold]}");
	$lbl->setLocation(1,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('extensionfamille');
	$edt->setValue($extension->Famille);
	$edt->setLocation(2,2,5);
	$xfer_result->addComponent($edt);
}

	$lbl=new Xfer_Comp_LabelForm('Versionlbl');
	$lbl->setValue("{[bold]}Version{[/bold]}");
	$lbl->setLocation(1,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('version_max',0,10000,0);
	$edt->setValue($extension->Version[0]);
	$edt->setLocation(2,3);
	$edt->setSize(20,20);
	$xfer_result->addComponent($edt);
	$edt=new Xfer_Comp_Float('version_min',0,10000,0);
	$edt->setValue($extension->Version[1]);
	$edt->setLocation(3,3);
	$edt->setSize(20,20);
	$xfer_result->addComponent($edt);
	$edt=new Xfer_Comp_Float('version_release',1,10000,0);
	$edt->setValue($extension->Version[2]);
	$edt->setLocation(4,3);
	$edt->setSize(20,20);
	$xfer_result->addComponent($edt);
	$edt=new Xfer_Comp_Float('version_build',1,10000,0);
	$edt->setValue($extension->Version[3]);
	$edt->setLocation(5,3,2);
	$edt->setSize(20,20);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('extensiondesclbl');
	$lbl->setValue("{[bold]}Description{[/bold]}");
	$lbl->setLocation(1,4);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Memo('extensiondesc');
	$edt->setValue(urlencode(str_replace(array('{[newline]}'),array("\n"),$extension->Description)));
	$edt->setLocation(2,4,5);
	$edt->setSize(75,75);
	$edt->Needed=true;
	$edt->Encode=true;
	$xfer_result->addComponent($edt);

if ($extension->Name!='CORE') {
	$lbl=new Xfer_Comp_LabelForm('extensionlibrelbl');
	$lbl->setValue("{[bold]}Libre{[/bold]}");
	$lbl->setLocation(5,0);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('extensionlibre');
	$edt->setValue($extension->Libre=='o');
	$edt->setLocation(6,0);
	$edt->Needed=true;
	$xfer_result->addComponent($edt);
}

if (!$ReadOnly) {
	$btn=new Xfer_Comp_Button('modif');
	$btn->setAction(new Xfer_Action("_Modifier","",$extensionname,'modifExtension',FORMTYPE_MODAL,CLOSE_NO));
	$btn->setLocation(1,8,6);
	$xfer_result->addComponent($btn);
}
	$btn=new Xfer_Comp_Button('refreshBtn');
	$btn->setAction(new Xfer_Action("_Rafraichir","",$extensionname,'refreshExtension',FORMTYPE_MODAL,CLOSE_NO));
	$btn->setLocation(1,9,6);
	$xfer_result->addComponent($btn);

	$lbl=new Xfer_Comp_Label('marge3');
	$lbl->setValue("");
	$lbl->setSize(20,10);
	$lbl->setLocation(1,10,6);
	$xfer_result->addComponent($lbl);

//====================================================================================================================

if ($extensionname!="applis") {
//#############################################
	$xfer_result->newTab("Les Menus");
	$lbl=new Xfer_Comp_LabelForm('menulbl');
	$lbl->setValue("{[bold]}{[center]}Les Menus{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('menu');
	$grid->newHeader('A',"Description",4);
	$grid->newHeader('B',"#",0);
	$grid->newHeader('C',"Aide",4);
	$grid->newHeader('D',"Action",4);
	$grid->newHeader('E',"Père",4);
	$grid->newHeader('F',"Icon",4);
	$grid->newHeader('G',"Raccourcis",4);
	$grid->newHeader('H',"Modal",3);
	foreach($extension->Menus as $key => $Menu) {
		$img="";
		require_once "Class/Image.inc.php";
		$img_mng=new ImageManage();
		$dir_img=$img_mng->GetExtDir($extensionname);
		if (is_file($dir_img.$Menu->icon))
			$img=$Menu->icon;
		if ($Menu->modal!=0) $modal_txt='oui'; else $modal_txt='n';
		$menu_list[$Menu->pere.$Menu->position."$key"]=array($key, $Menu->description, $Menu->position, $Menu->help, $Menu->act, $Menu->pere, $img,$Menu->shortcut, $modal_txt);
	}
	ksort($menu_list);
	foreach($menu_list as $pere => $val){
		$grid->setValue($val[0],'A',$val[1]);
		$grid->setValue($val[0],'B',$val[2]);
		$grid->setValue($val[0],'C',$val[3]);
		$grid->setValue($val[0],'D',$val[4]);
		$grid->setValue($val[0],'E',$val[5]);
		$grid->setValue($val[0],'F',$val[6]);
		$grid->setValue($val[0],'G',$val[7]);
		$grid->setValue($val[0],'H',$val[8]);
	}
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"addMenu",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteMenu",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addMenu",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);
}

//#############################################
	$xfer_result->newTab("Les Droits");
	$lbl=new Xfer_Comp_LabelForm('rightlbl');
	$lbl->setValue("{[bold]}{[center]}Les Droits{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('right');
	$grid->newHeader('A',"N°",0);
	$grid->newHeader('B',"Nom",4);
	$grid->newHeader('C',"Poids",1);
	foreach($extension->Rights as $key => $Right) {
		$grid->setValue($key,'A',$key+1);
		$grid->setValue($key,'B',$Right->description);
		$grid->setValue($key,'C',$Right->weigth);
	}
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"addRight",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteRight",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addRight",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

//#############################################
	$xfer_result->newTab("Les signaux");
	$lbl=new Xfer_Comp_LabelForm('signallbl');
	$lbl->setValue("{[bold]}{[center]}Les signaux{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('signal');
	$grid->newHeader('A',"Identifiant",4);
	$grid->newHeader('B',"Parametres",4);
	$grid->newHeader('C',"Description",4);
	foreach($extension->Signals as $key => $Signal) {
		$grid->setValue($key,'A',$Signal[0]);
		$grid->setValue($key,'B',$Signal[1]);
		$grid->setValue($key,'C',$Signal[2]);
	}
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"addSignal",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteSignal",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addSignal",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

//#############################################
	require_once("FunctionTool.inc.php");
	$xfer_result->newTab("Les Paramètres");
	$lbl=new Xfer_Comp_LabelForm('paramlbl');
	$lbl->setValue("{[bold]}{[center]}Les Paramètres{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('param');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Description",4);
	$grid->newHeader('C',"Type",4);
	$grid->newHeader('D',"Paramètres",4);
	$grid->newHeader('E',"Valeur",4);

	require_once("../CORE/setup_param.inc.php");
	$param_dico=array();
	$param_dico[PARAM_TYPE_STR]="Text";
	$param_dico[PARAM_TYPE_INT]="Entier";
	$param_dico[PARAM_TYPE_REAL]="Réel";
	$param_dico[PARAM_TYPE_BOOL]="Booléan";
	$param_dico[PARAM_TYPE_ENUM]="Enumération";

	foreach($extension->Params as $key => $Param){
		$grid->setValue($key,'A',$key);
		$grid->setValue($key,'B',$Param->description);
		$grid->setValue($key,'C',$param_dico[$Param->type]);
		$grid->setValue($key,'D',ArrayToString($Param->extend,true));
		$grid->setValue($key,'E',$Param->defaultvalue);
	}
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"addParam",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteParam",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addParam",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

if ($extensionname!="applis") {
//#############################################
	$xfer_result->newTab("Les Dépendences");
	$lbl=new Xfer_Comp_LabelForm('dependlbl');
	$lbl->setValue("{[bold]}{[center]}Les Dépendences{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('depend');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Majeur max",0);
	$grid->newHeader('C',"Mineur max",0);
	$grid->newHeader('D',"Majeur min",0);
	$grid->newHeader('E',"Mineur min",0);
	$grid->newHeader('F',"Optionnel",3);
	foreach($extension->Depencies as $key => $Depency) {
		$grid->setValue($key,'A',$Depency->name);
		$grid->setValue($key,'B',$Depency->version_majeur_max);
		$grid->setValue($key,'C',$Depency->version_mineur_max);
		$grid->setValue($key,'D',$Depency->version_majeur_min);
		$grid->setValue($key,'E',$Depency->version_mineur_min);
		$grid->setValue($key,'F',$Depency->optionnal?'oui':'n');
	}
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"addDepend",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteDepend",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addDepend",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);
}
//#############################################
	$xfer_result->newTab("Les Images");
	$lbl=new Xfer_Comp_LabelForm('imagelbl');
	$lbl->setValue("{[bold]}{[center]}Les Images{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	require_once("Class/Image.inc.php");
	$mng=new ImageManage();
	$grid=new Xfer_Comp_Grid('image');
	$grid->newHeader('A',"Image",100);
	$grid->newHeader('B',"Nom",4);
	$extdir=$mng->GetExtDir($extensionname);
	foreach($extension->GetImageList() as $img)
		if (is_file($extdir.$img)){
			$grid->setValue($img,'A',$extdir.$img);
			$grid->setValue($img,'B',$img);
	}
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteImage",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addImage",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

//#############################################
	require_once("Class/Help.inc.php");
	$xfer_result->newTab("Les Aides");
	$lbl=new Xfer_Comp_LabelForm('main_helplbl');
	$lbl->setValue("{[bold]}{[center]}Les Aides{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,3);
	$xfer_result->addComponent($lbl);
	$help_mng=new HelpManage($extensionname);
	$extdir=$help_mng->GetExtDir($extensionname);

	$lbl=new Xfer_Comp_LabelForm('help_titlebl');
	$lbl->setValue("{[bold]}Titre de l'aide{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('help_title');
	$edt->setValue($help_mng->HelpTitle);
	$edt->setLocation(1,1);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('help_positionlbl');
	$lbl->setValue("{[bold]}Position l'aide{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('help_position',0,10000,0);
	$edt->setValue($help_mng->HelpPosition);
	$edt->setLocation(1,2);
	$xfer_result->addComponent($edt);

if (!$ReadOnly) {
	$btn=new Xfer_Comp_Button('modif');
	$btn->setAction(new Xfer_Action("_Modifier","",$extensionname,'modifHelp',FORMTYPE_MODAL,CLOSE_NO));
	$btn->setLocation(0,3,2);
	$xfer_result->addComponent($btn);
}

	$lbl=new Xfer_Comp_Label('marge4');
	$lbl->setValue("");
	$lbl->setSize(10,300);
	$lbl->setLocation(2,1,1,2);
	$xfer_result->addComponent($lbl);

	$grid=new Xfer_Comp_Grid('help');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Description",4);
	$grid->newHeader('C',"Ordre",4);
	foreach($help_mng->HelpDescriptions as $id=>$Help) {
		$cd=new Help($Code,$extensionname);
		$grid->setValue($Help[0],'A',$Help[0]);
		$grid->setValue($Help[0],'B',$Help[1]);
		if ($Help[2]==1)
			$grid->setValue($Help[0],'C',$id+1);
		else
			$grid->setValue($Help[0],'C','-');
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"addHelp",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteHelp",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addHelp",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,4,3);
	$xfer_result->addComponent($grid);

	$grid=new Xfer_Comp_Grid('imageHelp');
	$grid->newHeader('A',"Image",100);
	$grid->newHeader('B',"Nom",4);
	$extdir=$help_mng->GetExtDir($extensionname);
	foreach($help_mng->GetImageList($extensionname) as $img)
		if (is_file($extdir.$img)){
			$grid->setValue($img,'A',$extdir.$img);
			$grid->setValue($img,'B',$img);
	}
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteImageHelp",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addImageHelp",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,5,3);
	$xfer_result->addComponent($grid);

//====================================================================================================================

	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
 
